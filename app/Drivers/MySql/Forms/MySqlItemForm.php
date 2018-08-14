<?php

namespace UniMan\Drivers\Mysql\Forms;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\TextArea;
use Nette\Utils\ArrayHash;
use PDO;
use UniMan\Core\Forms\ItemForm\ItemFormInterface;
use UniMan\Drivers\MySql\MySqlDataManager;

class MySqlItemForm implements ItemFormInterface
{
    private $pdo;

    private $dataManager;

    private $type;

    private $table;

    private $item;

    private $columns = [];

    public function __construct(PDO $pdo, MySqlDataManager $dataManager, $type, $table, $item)
    {
        $this->pdo = $pdo;
        $this->dataManager = $dataManager;
        $this->type = $type;
        $this->table = $table;
        $this->item = $item;
    }

    public function addFieldsToForm(Form $form)
    {
        $this->columns = $this->dataManager->getColumns($this->type, $this->table);
        foreach ($this->columns as $column => $definition) {
            if (isset($definition['key_info']['REFERENCED_TABLE_NAME'])) {
                $items = [];
                foreach ($this->dataManager->items($this->type, $definition['key_info']['REFERENCED_TABLE_NAME'], 1, PHP_INT_MAX) as $item) {
                    $items[$item[$definition['key_info']['REFERENCED_COLUMN_NAME']]] = implode(' ', $item);
                }
                $field = $form->addSelect($column, $column, $items);
                $field->setAttribute('class', 'js-select2');
                $field->setPrompt('');
            } elseif ($definition['Type'] === 'datetime') {
                $field = $form->addDateTimePicker($column, $column);
            } elseif ($definition['Type'] === 'date') {
                $field = $form->addDatePicker($column, $column);
            } elseif (strpos($definition['Type'], 'text') !== false) {
                $field = $form->addTextArea($column, $column, null, 7);
            } elseif ($definition['Type'] === 'tinyint(1)') {
                $field = $form->addCheckbox($column, $column);
            } else {
                $field = $form->addText($column, $column);
                if (strpos($definition['Type'], 'int') !== false) {
                    $field->addCondition(Form::FILLED)
                        ->addRule(Form::INTEGER, 'mysql.item_form.field.integer');
                }
            }

            if ($definition['Extra'] == 'auto_increment') {
                $field->setAttribute('placeholder', 'autoincrement');
            } elseif (!$field instanceof Checkbox && !$field instanceof TextArea && $definition['Null'] === 'NO') {
                $field->setRequired('mysql.item_form.field.required');
            }
        }

        if ($this->item) {
            $item = $this->dataManager->loadItem($this->type, $this->table, $this->item);
            if ($item) {
                $form->setDefaults($item);
            }
        }
    }

    public function submit(Form $form, ArrayHash $values)
    {
        $values = (array)$values;
        $keys = array_map(function ($key) {
            return '`' . $key . '`';
        }, array_keys($values));
        $vals = array_map(function ($key) {
            return ':' . $key;
        }, array_keys($values));
        if ($this->item) {
            $query = sprintf('UPDATE `%s` SET ', $this->table);
            $set = [];
            foreach ($values as $key => $value) {
                $set[] = '`' . $key . '` = :' . $key;
            }
            $query .= implode(', ', $set);
            $primaryColumns = $this->dataManager->getPrimaryColumns($this->type, $this->table);
            $query .= sprintf(' WHERE md5(concat(%s)) = "%s"', implode(', "|", ', $primaryColumns), $this->item);
        } else {
            $query = sprintf('INSERT INTO `%s` %s VALUES %s', $this->table, '(' . implode(', ', $keys) . ')', '(' . implode(', ', $vals) . ')');
        }
        $statement = $this->pdo->prepare($query);
        foreach ($values as $key => $value) {
            $value = $value === '' && $this->columns[$key]['Null'] ? null : $value;
            $statement->bindValue(':' . $key, $value);
        }
        $ret = $statement->execute();
        if (!$ret) {
            $form->addError($statement->errorInfo()[2]);
            return;
        }
        return $ret;
    }
}
