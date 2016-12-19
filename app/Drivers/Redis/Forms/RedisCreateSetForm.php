<?php

namespace Adminerng\Drivers\Redis\Forms;

use Adminerng\Core\Forms\TableForm\TableFormInterface;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use RedisProxy\RedisProxy;

class RedisCreateSetForm implements TableFormInterface
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
        $form->addText('key', 'redis.set_form.key.label')
            ->setRequired('redis.set_form.key.required');
        $form->addText('members', 'redis.set_form.members.label')
            ->setRequired('redis.set_form.members.required')
            ->setOption('description', 'redis.set_form.members.description');
        if ($this->key) {
            $form->setDefaults([
                'key' => $this->key,
            ]);
        }
    }

    public function submit(Form $form, ArrayHash $values)
    {
        $key = $values['key'];
        $members = array_map('trim', explode(',', $values['members']));
        call_user_func_array([$this->connection, 'sadd'], array_merge([$key], $members));
    }
}
