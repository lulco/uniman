<?php

namespace Adminerng\Drivers\Redis\Forms;

use Adminerng\Core\Forms\ItemForm\ItemFormInterface;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use PDO;

class MySqlItemForm implements ItemFormInterface
{
    private $pdo;

    private $table;

    private $item;

    public function __construct(PDO $pdo, $table, $item)
    {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->item = $item;
    }

    public function addFieldsToForm(Form $form)
    {
        foreach ($this->pdo->query('SHOW FULL COLUMNS FROM `' . $this->table .'`')->fetchAll(PDO::FETCH_ASSOC) as $column) {
            $form->addText($column['Field'], $column['Field']);
        }

        if ($this->item) {
            // TODO
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
