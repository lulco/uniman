<?php

namespace Adminerng\Drivers\Memcache;

use Adminerng\Core\CredentialsFormInterface;
use Nette\Application\UI\Form;

class MemcacheForm implements CredentialsFormInterface
{
    public function addFieldsToForm(Form $form)
    {
        $form->addText('host', 'Host')
            ->setAttribute('placeholder', 'localhost');
        $form->addText('port', 'Port')
            ->setAttribute('placeholder', '11211');
    }
}
