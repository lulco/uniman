<?php

namespace Adminerng\Drivers\Rabbit;

use Adminerng\Core\CredentialsFormInterface;
use Nette\Application\UI\Form;

class RabbitForm implements CredentialsFormInterface
{
    public function addFieldsToForm(Form $form)
    {
        $form->addText('user', 'User')
            ->setAttribute('placeholder', 'guest');
        $form->addPassword('password', 'Password')
            ->setAttribute('placeholder', 'guest');;
        $form->addText('host', 'Host')
            ->setAttribute('placeholder', 'localhost');
        $form->addText('port', 'Port')
            ->setAttribute('placeholder', '5672');
        $form->addText('api_host', 'RabbitMQ Management HTTP API host')
            ->setAttribute('placeholder', 'localhost')
            ->setOption('description', 'rabbitmq_management plugin must be enabled');
        $form->addText('api_port', 'RabbitMQ Management HTTP API port')
            ->setAttribute('placeholder', '15672')
            ->setOption('description', 'rabbitmq_management plugin must be enabled');
    }
}
