<?php

namespace Adminerng\Drivers\Redis\Forms;

use Adminerng\Core\Forms\ItemForm\ItemFormInterface;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use RedisProxy\RedisProxy;

// TODO use data manager instead of connection
class RedisHashKeyItemForm implements ItemFormInterface
{
    private $connection;

    private $hash;

    private $key;

    public function __construct(RedisProxy $connection, $hash, $key)
    {
        $this->connection = $connection;
        $this->hash = $hash;
        $this->key = $key;
    }

    public function addFieldsToForm(Form $form)
    {
        $form->addText('key', 'redis.key_form.key.label')
            ->setRequired('redis.key_form.key.required');
        $form->addText('value', 'redis.key_form.value.label');

        if ($this->key) {
            $form->setDefaults([
                'key' => $this->key,
                'value' => $this->connection->hget($this->hash, $this->key),
            ]);
        }
    }

    public function submit(Form $form, ArrayHash $values)
    {
        if ($this->key) {
            $this->connection->hdel($this->hash, $this->key);
        }
        $this->connection->hset($this->hash, $values['key'], $values['value']);
    }
}
