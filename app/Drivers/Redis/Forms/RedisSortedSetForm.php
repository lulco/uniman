<?php

namespace UniMan\Drivers\Redis\Forms;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use RedisProxy\RedisProxy;
use UniMan\Core\Forms\TableForm\TableFormInterface;

class RedisSortedSetForm implements TableFormInterface
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
        $form->addText('key', 'redis.sorted_set_form.key.label')
            ->setRequired('redis.sorted_set_form.key.required');

        $form->addTextArea('members', 'redis.sorted_set_form.members.label')
            ->setOption('description', 'redis.sorted_set_form.members.description')
            ->setRequired('redis.sorted_set_form.members.required');

        if ($this->key) {
            $members = [];
            foreach ($this->connection->zrange($this->key, 0, -1, true) as $member => $score) {
                $members[] = $member . ':' . $score;
            }
            $form->setDefaults([
                'key' => $this->key,
                'members' => implode(',', $members),
            ]);
        }
    }

    public function submit(Form $form, ArrayHash $values)
    {
        if ($this->key && $this->key !== $values->key) {
            $this->connection->rename($this->key, $values->key);
        }

        $members = [];
        foreach (array_map('trim', explode(',', $values->members)) as $item) {
            $member = $item;
            $score = 0;
            if (strpos($item, ':')) {
                list($member, $score) = explode(':', $item, 2);
            }
            $members[trim($member)] = floatval(trim($score));
        }
        $this->connection->zadd($values->key, $members);
    }
}
