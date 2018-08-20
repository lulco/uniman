<?php

namespace UniMan\Drivers\Redis\Forms;

use UniMan\Core\Forms\ItemForm\ItemFormInterface;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use RedisProxy\RedisProxy;

class RedisSortedSetMemberForm implements ItemFormInterface
{
    private $connection;

    private $key;

    private $member;

    public function __construct(RedisProxy $connection, $key, $member)
    {
        $this->connection = $connection;
        $this->key = $key;
        $this->member = $member;
    }

    public function addFieldsToForm(Form $form)
    {
        $form->addText('member', 'redis.sorted_set_member_form.member.label')
            ->setRequired('redis.sorted_set_member_form.member.required');

        $form->addText('score', 'redis.sorted_set_member_form.score.label')
            ->setRequired('redis.sorted_set_member_form.score.required');

        if ($this->member !== null) {
            $form->setDefaults([
                'member' => $this->member,
                'score' => $this->connection->zscore($this->key, $this->member),
            ]);
        }
    }

    public function submit(Form $form, ArrayHash $values)
    {
        if ($this->member !== $values->member) {
            $this->connection->zrem($this->key, $this->member);
        }
        $this->connection->zadd($this->key, [$values->member => str_replace(',', '.', $values->score)]);
    }
}
