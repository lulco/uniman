<?php

namespace UniMan\Drivers\Redis\Forms;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use RedisProxy\RedisProxy;
use UniMan\Core\Forms\TableForm\TableFormInterface;

class RedisListForm implements TableFormInterface
{
    private $connection;

    private $key;

    public function __construct(RedisProxy $connection, string $key = null)
    {
        $this->connection = $connection;
        $this->key = $key;
    }

    public function addFieldsToForm(Form $form)
    {
        $form->addText('key', 'redis.list_form.key.label')
            ->setRequired('redis.list_form.key.required');

        $form->addText('elements', 'redis.list_form.elements.label')
            ->setRequired('redis.list_form.elements.required')
            ->setOption('description', 'redis.list_form.elements.description');

        if ($this->key) {
            $form->setDefaults([
                'key' => $this->key,
                'elements' => implode(',', $this->connection->lrange($this->key, 0, -1)),
            ]);
        }
    }

    public function submit(Form $form, ArrayHash $values)
    {
        if ($this->key) {
            $this->connection->del($this->key);
        }

        $elements = array_map('trim', explode(',', $values->elements));
        $this->connection->rpush($values->key, $elements);
    }
}
