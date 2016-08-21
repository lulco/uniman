<?php

namespace Adminerng\Drivers\Redis;

use Adminerng\Core\CredentialsFormInterface;
use Nette\Application\UI\Form;

class RedisForm implements CredentialsFormInterface
{
    public function addFieldsToForm(Form $form)
    {
        $form->addText('host', 'Host')
            ->setAttribute('placeholder', 'localhost');
        $form->addText('port', 'Port')
            ->setAttribute('placeholder', '6379');
        $form->addText('database', 'Database')
            ->setAttribute('placeholder', 0);
    }
}
