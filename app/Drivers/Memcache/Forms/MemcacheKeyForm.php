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
            $this->connection->delete($this->key);
        }
        $this->connection->set($values['key'], $values['value']);
    }
}
