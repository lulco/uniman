<?php

namespace UniMan\Drivers\Redis\Forms;

use UniMan\Core\Forms\ItemForm\ItemFormInterface;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use RedisProxy\RedisProxy;

class RedisKeyItemForm implements ItemFormInterface
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
        $form->addText('key', 'redis.item_form.key.label')
            ->setRequired('redis.item_form.key.required');
        $form->addText('value', 'redis.item_form.value.label');

        if ($this->key) {
            $form->setDefaults([
                'key' => $this->key,
                'value' => $this->connection->get($this->key),
            ]);
        }
    }

    public function submit(Form $form, ArrayHash $values)
    {
        if ($this->key) {
            $this->connection->del($this->key);
        }
        $this->connection->set($values['key'], $values['value']);
    }
}
