<?php

namespace Adminerng\Drivers\Redis\Forms;

use Adminerng\Core\Forms\TableForm\TableFormInterface;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use RedisProxy\RedisProxy;

class RedisCreateHashForm implements TableFormInterface
{
    private $connection;

    public function __construct(RedisProxy $connection)
    {
        $this->connection = $connection;
    }

    public function addFieldsToForm(Form $form)
    {
        $form->addText('key', 'redis.hash_form.key.label')
            ->setRequired('redis.hash_form.key.required');
        $form->addText('field', 'redis.hash_form.field.label')
            ->setRequired('redis.hash_form.field.required');
        $form->addText('value', 'redis.hash_form.value.label')
            ->setRequired('redis.hash_form.value.required');
    }

    public function submit(Form $form, ArrayHash $values)
    {
        if ($this->connection->hlen($values['key']) > 0) {
            $form->addError('Key "' . $values['key'] . '" already exists');
            return;
        }
        $this->connection->hset($values['key'], $values['field'], $values['value']);
    }
}
