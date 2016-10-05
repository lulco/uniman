<?php

namespace Adminerng\Drivers\Redis\Forms;

use Adminerng\Core\Forms\TableForm\TableFormInterface;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use RedisProxy\RedisProxy;

class RedisRenameHashForm implements TableFormInterface
{
    private $connection;

    private $key;

    public function __construct(RedisProxy $connection, $key)
    {
        $this->connection = $connection;
        $this->key = $key;
    }

    public function addFieldsToForm(Form $form)
    {
        $form->addText('key', 'redis.hash_form.key.label')
            ->setRequired('redis.hash_form.key.required')
            ->setDisabled()
            ->setValue($this->key);
        $form->addText('new_key', 'redis.hash_form.new_key.label')
            ->setRequired('redis.hash_form.new_key.required');
    }

    public function submit(Form $form, ArrayHash $values)
    {
        if (!$this->connection->rename($this->key, $values['new_key'])) {
            $form->addError('Key "' . $this->key . '" doesn\'t exist');
            return;
        }
    }
}
