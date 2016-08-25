<?php

namespace Adminerng\Drivers\Memcache;

use Adminerng\Core\CredentialsFormInterface;
use Nette\Application\UI\Form;

class MemcacheForm implements CredentialsFormInterface
{
    public function addFieldsToForm(Form $form)
    {
        $form->addTextArea('servers', 'Servers')
            ->setAttribute('placeholder', 'localhost:11211')
            ->setOption('description', 'Each server on new line in format: host:port');
    }
}
