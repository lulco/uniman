<?php

namespace Adminerng\Drivers\Rabbit;

use Adminerng\Core\CredentialsFormInterface;
use Nette\Application\UI\Form;

class RabbitForm implements CredentialsFormInterface
{
    public function addFieldsToForm(Form $form)
    {
        $form->addText('user', 'rabbit.form.user.label')
            ->setAttribute('placeholder', 'guest');
        $form->addPassword('password', 'rabbit.form.password.label')
            ->setAttribute('placeholder', 'guest');
        $form->addText('host', 'rabbit.form.host.label')
            ->setAttribute('placeholder', 'localhost');
        $form->addText('port', 'rabbit.form.port.label')
            ->setAttribute('placeholder', '5672');
        $form->addText('api_host', 'rabbit.form.rabbitmq_management_http_api_host.label')
            ->setAttribute('placeholder', 'localhost')
            ->setOption('description', 'rabbit.form.rabbitmq_management_http_api_host.description');
        $form->addText('api_port', 'rabbit.form.rabbitmq_management_http_api_port.label')
            ->setAttribute('placeholder', '15672')
            ->setOption('description', 'rabbit.form.rabbitmq_management_http_api_port.description');
    }
}
