<?php

namespace Adminerng\Drivers\Memcache\Forms;

use Adminerng\Core\Forms\TableForm\TableFormInterface;
use Memcache;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

class MemcacheKeyForm implements TableFormInterface
{
    private $connection;

    private $key;

    public function __construct(Memcache $connection, $key = null)
    {
        $this->connection = $connection;
        $this->key = $key;
    }

    public function addFieldsToForm(Form $form)
    {
        $form->addText('key', 'memcache.key_form.key.label')
            ->setRequired('memcache.key_form.key.required');
        $form->addText('value', 'memcache.key_form.value.label')
            ->setRequired('memcache.key_form.value.required');
        $form->addCheckbox('compressed', 'memcache.key_form.compressed.label');
        $form->addText('expiration', 'memcache.key_form.expiration.label')
            ->addCondition(Form::FILLED)
            ->addRule(Form::INTEGER, 'memcache.key_form.expiration.rule_integer');

        if ($this->key) {
            $flags = false;
            $form->setDefaults([
                'key' => $this->key,
                'value' => $this->connection->get($this->key, $flags),
                'compressed' => $flags == MEMCACHE_COMPRESSED,
            ]);
        }
    }

    public function submit(Form $form, ArrayHash $values)
    {
        if ($this->key) {
            $this->connection->delete($this->key);
        }
        $this->connection->set($values['key'], $values['value'], $values['compressed'] ? MEMCACHE_COMPRESSED : 0, $values['expiration'] ? intval($values['expiration']) : null);
    }
}
