<?php

namespace Adminerng\Drivers\Redis\Forms;

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
        $columns = $this->dataManager->getColumns($this->type, $this->table);
        foreach ($columns as $column) {
            $this->columns[$column['Field']] = $column;
            if ($column['Type'] === 'datetime') {
                $field = $form->addDateTimePicker($column['Field'], $column['Field']);
            } elseif ($column['Type'] === 'date') {
                $field = $form->addDatePicker($column['Field'], $column['Field']);
            } elseif ($column['Type'] === 'text') {
                $field = $form->addTextArea($column['Field'], $column['Field'], null, 7);
            } elseif ($column['Type'] === 'tinyint(1)') {
                $field = $form->addCheckbox($column['Field'], $column['Field']);
            } else {
                $field = $form->addText($column['Field'], $column['Field']);
                if (strpos($column['Type'], 'int') !== false) {
                    $field->addCondition(Form::FILLED)
                        ->addRule(Form::INTEGER, 'mysql.item_form.field.integer');
                }
            }

            if (!$field instanceof Checkbox && $column['Null'] === 'NO') {
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
            $primaryColumns = $this->dataManager->getPrimaryColumns(null, $this->table);
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
