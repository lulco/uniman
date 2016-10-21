<?php

namespace Adminerng\Drivers\Memcache\Forms;

use Adminerng\Core\CredentialsFormInterface;
use Nette\Application\UI\Form;

class MemcacheCredentialsForm implements CredentialsFormInterface
{
    public function addFieldsToForm(Form $form)
    {
        $form->addTextArea('servers', 'memcache.form.servers.label')
            ->setAttribute('placeholder', 'localhost:11211')
            ->setOption('description', 'memcache.form.servers.description');
    }
}
