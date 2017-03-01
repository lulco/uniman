<?php

namespace UniMan\Drivers\Redis\Forms;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use UniMan\Core\Forms\DatabaseForm\DatabaseFormInterface;
use UniMan\Drivers\Redis\RedisDatabaseAliasStorage;

class RedisEditDatabaseForm implements DatabaseFormInterface
{
    private $database;

    private $databaseAliasStorage;

    public function __construct($database, RedisDatabaseAliasStorage $databaseAliasStorage)
    {
        $this->database = $database;
        $this->databaseAliasStorage = $databaseAliasStorage;
    }

    public function addFieldsToForm(Form $form)
    {
        $form->addText('database', 'redis.database_form.database.label')
            ->setRequired('redis.database_form.database.required')
            ->setDisabled();

        $form->addText('alias', 'redis.database_form.alias.label')
            ->setOption('description', 'redis.database_form.alias.description');

        $form->setDefaults([
            'database' => $this->database,
            'alias' => $this->databaseAliasStorage->load($this->database),
        ]);
    }

    public function submit(Form $form, ArrayHash $values)
    {
        $aliases = $this->databaseAliasStorage->loadAll();
        $aliases[$this->database] = $values['alias'];
        if (!$values['alias']) {
            unset($aliases[$this->database]);
        }
        return $this->databaseAliasStorage->save($aliases);
    }
}
