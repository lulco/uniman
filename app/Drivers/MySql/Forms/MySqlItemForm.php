<?php

namespace UniMan\Drivers\Mysql\Forms;

use UniMan\Core\Forms\ItemForm\ItemFormInterface;
use UniMan\Drivers\MySql\MySqlDataManager;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\Checkbox;
use Nette\Utils\ArrayHash;
use PDO;

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
            if ($definition['Type'] === 'datetime') {
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
            } elseif (!$field instanceof Checkbox && $definition['Null'] === 'NO') {
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
