<?php

namespace Adminerng\Drivers\Memcache;

use Adminerng\Core\CredentialsFormInterface;
use Nette\Application\UI\Form;

class MemcacheForm implements CredentialsFormInterface
{
    public function addFieldsToForm(Form $form)
    {
        $form->addTextArea('servers', 'memcache.form.servers.label')
            ->setAttribute('placeholder', 'localhost:11211')
            ->setOption('description', 'memcache.form.servers.description');
    }
}
