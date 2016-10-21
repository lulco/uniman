<?php

namespace Adminerng\Drivers\Mysql\Forms;

use Adminerng\Core\Forms\ItemForm\ItemFormInterface;
use Adminerng\Drivers\MySql\MySqlDataManager;
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
        $columns = $this->dataManager->getColumns($this->type, $this->table);
        foreach ($columns as $column => $definition) {
            if ($definition['Type'] === 'datetime') {
                $field = $form->addDateTimePicker($column, $column);
            } elseif ($definition['Type'] === 'date') {
                $field = $form->addDatePicker($column, $column);
            } elseif ($definition['Type'] === 'text') {
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

            if (!$field instanceof Checkbox && $definition['Null'] === 'NO') {
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
            $query = 'UPDATE `' . $this->table . '` SET ';
            $set = [];
            foreach ($values as $key => $value) {
                $set[] = '`' . $key . '` = :' . $key;
            }
            $query .= implode(', ', $set);
            $primaryColumns = $this->dataManager->getPrimaryColumns($this->type, $this->table);
            $query .= ' WHERE md5(concat(' . implode(', "|", ', $primaryColumns) . ')) = "' . $this->item . '"';
        } else {
            $query = sprintf('INSERT INTO `' . $this->table . '` %s VALUES %s', '(' . implode(', ', $keys) . ')', '(' . implode(', ', $vals) . ')');
        }
        $statement = $this->pdo->prepare($query);
        foreach ($values as $key => $value) {
            $statement->bindValue(':' . $key, $value);
        }
        return $statement->execute();
    }
}
