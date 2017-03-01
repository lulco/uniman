<?php

namespace UniMan\Drivers\Redis\Forms;

use UniMan\Core\Forms\ItemForm\ItemFormInterface;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use RedisProxy\RedisProxy;

class RedisSetMemberForm implements ItemFormInterface
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
        $form->addText('member', 'redis.member_form.member.label')
            ->setRequired('redis.member_form.member.required');

        if ($this->member) {
            $form->setDefaults([
                'member' => $this->member,
            ]);
        }
    }

    public function submit(Form $form, ArrayHash $values)
    {
        if ($this->member && !$this->connection->srem($this->key, $this->member)) {
            $form->addError($form->getTranslator()->translate('redis.member_form.message.cannot_be_removed'));
            return;
        }
        $this->connection->sadd($this->key, $values['member']);
    }
}
