<?php

namespace Adminerng\Drivers\Redis\Forms;

use Adminerng\Core\Forms\ItemForm\ItemFormInterface;
use Adminerng\Drivers\MySql\MySqlDataManager;
use Nette\Application\UI\Form;
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
//        print_R($columns);
//        exit();
        foreach ($columns as $column) {
            if ($column['Type'] === 'text') {
                $field = $form->addTextArea($column['Field'], $column['Field'], null, 7);
            } elseif ($column['Type'] === 'tinyint(1)') {
                $field = $form->addCheckbox($column['Field'], $column['Field']);
            } else {
                $field = $form->addText($column['Field'], $column['Field']);
            }
            if ($column['Null'] === 'NO') {
                $field->setRequired();
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
        if ($this->item) {
            // UPDATE
        } else {
            $keys = array_map(function ($key) {
                return '`' . $key . '`';
            }, array_keys((array)$values));
            $vals = array_map(function ($key) {
                return ':' . $key;
            }, array_keys((array)$values));
            $query = sprintf('INSERT INTO `' . $this->table . '` %s VALUES %s', '(' . implode(', ', $keys) . ')', '(' . implode(', ', $vals) . ')');

            $statement = $this->pdo->prepare($query);
            foreach ($values as $key => $value) {
                $statement->bindValue(':' . $key, $value);
            }
            $statement->execute();
        }
    }
}
