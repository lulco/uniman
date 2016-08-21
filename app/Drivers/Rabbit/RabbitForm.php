<?php

namespace Adminerng\Drivers\Rabbit;

use Adminerng\Core\CredentialsFormInterface;
use Nette\Application\UI\Form;

class RabbitForm implements CredentialsFormInterface
{
    public function addFieldsToForm(Form $form)
    {
        $form->addText('host', 'Host')
            ->setAttribute('placeholder', 'localhost');
        $form->addText('port', 'Port')
            ->setAttribute('placeholder', '5672');
        $form->addText('user', 'User')
            ->setAttribute('placeholder', 'guest');
        $form->addPassword('password', 'Password')
            ->setAttribute('placeholder', 'guest');
        $form->addText('vhost', 'Vhost')
            ->setAttribute('placeholder', '/');
        $form->addTextArea('queues', 'Queues');
    }
}
