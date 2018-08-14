<?php

namespace UniMan\Drivers\Mysql\Forms;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use PDO;
use UniMan\Core\Forms\DatabaseForm\DatabaseFormInterface;

class MySqlDatabaseForm implements DatabaseFormInterface
{
    private $pdo;

    private $database;

    public function __construct(PDO $pdo, $database)
    {
        $this->pdo = $pdo;
        $this->database = $database;
    }

    public function addFieldsToForm(Form $form)
    {
        $form->addText('name', 'Name')
            ->setRequired();

        $characterSets = $this->pdo->query('SELECT CHARACTER_SET_NAME, CONCAT(CHARACTER_SET_NAME, " (", DESCRIPTION, ")") FROM information_schema.CHARACTER_SETS ORDER BY CHARACTER_SET_NAME')->fetchAll(PDO::FETCH_KEY_PAIR);
        $form->addSelect('charset', 'Character set', $characterSets)
            ->setAttribute('class', 'js-select2')
            ->setPrompt('Default character set');

        $collations = [];
        foreach ($this->pdo->query('SELECT CHARACTER_SET_NAME, COLLATION_NAME FROM information_schema.COLLATIONS ORDER BY COLLATION_NAME')->fetchAll(PDO::FETCH_ASSOC) as $collation) {
            $collations[$collation['CHARACTER_SET_NAME']][$collation['COLLATION_NAME']] = $collation['COLLATION_NAME'];
        }
        ksort($collations);

        $form->addSelect('collation', 'Collation', $collations)
            ->setAttribute('class', 'js-select2')
            ->setPrompt('Default collation');

        if ($this->database) {
            $form['name']->setDisabled();
            $defaults = $this->pdo->query("SELECT `SCHEMA_NAME` AS `name`, `DEFAULT_CHARACTER_SET_NAME` AS `charset`, `DEFAULT_COLLATION_NAME` AS `collation` FROM `information_schema`.`SCHEMATA` WHERE `SCHEMA_NAME` = '{$this->database}'")->fetch(PDO::FETCH_ASSOC);
            $form->setDefaults($defaults);
        }
    }

    public function submit(Form $form, ArrayHash $values)
    {
        if ($this->database) {
            $query = 'ALTER DATABASE ' . $this->database;
        } else {
            $query = 'CREATE DATABASE ' . $values['name'];
        }
        if ($values['charset']) {
            $query .= ' CHARACTER SET ' . $values['charset'];
            if ($values['collation']) {
                $query .= ' COLLATE ' . $values['collation'];
            }
        }

        $statement = $this->pdo->prepare($query);
        $res = $statement->execute();
        if ($res === false) {
            $form->addError($statement->errorInfo()[2]);
            return;
        }
        return $res;
    }
}
