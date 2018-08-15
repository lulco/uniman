<?php

namespace UniMan\Drivers\Redis\Forms;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use RedisProxy\RedisProxy;
use UniMan\Core\Forms\ItemForm\ItemFormInterface;

class RedisListElementForm implements ItemFormInterface
{
    private $connection;

    private $key;

    private $index;

    public function __construct(RedisProxy $connection, string $key, string $index = null)
    {
        $this->connection = $connection;
        $this->key = $key;
        $this->index = $index;
    }

    public function addFieldsToForm(Form $form)
    {
        $form->addText('element', 'redis.list_element_form.element.label')
            ->setRequired('redis.list_element_form.element.required');

        if ($this->index !== null) {
            $element = $this->connection->lindex($this->key, $this->index);
            $form->setDefaults([
                'element' => $element,
            ]);
        }
    }

    public function submit(Form $form, ArrayHash $values)
    {
        if ($this->index !== null) {
            $this->connection->lset($this->key, $this->index, $values->element);
            return;
        }
        $this->connection->lpush($this->key, $values->element);
    }
}
