<?php

namespace Adminerng\Drivers\Redis\Forms;

use Adminerng\Core\Forms\TableForm\TableFormInterface;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use RedisProxy\RedisProxy;

class RedisEditSetForm implements TableFormInterface
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
                'members' => implode(',', $this->connection->smembers($this->key)),
            ]);
        }
    }

    public function submit(Form $form, ArrayHash $values)
    {
        $key = $values['key'];
        $actualMembers = $this->connection->smembers($this->key);
        $members = array_map('trim', explode(',', $values['members']));

        $membersToRemove = array_diff($actualMembers, $members);
        $membersToAdd = array_diff($members, $actualMembers);
        call_user_func_array([$this->connection, 'srem'], array_merge([$this->key], $membersToRemove));
        call_user_func_array([$this->connection, 'sadd'], array_merge([$this->key], $membersToAdd));

        if ($this->key != $key) {
            $this->connection->rename($this->key, $key);
        }
    }
}
