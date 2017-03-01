<?php

namespace UniMan\Drivers\RabbitMQ\Forms;

use UniMan\Core\CredentialsFormInterface;
use Nette\Application\UI\Form;

class RabbitMQCredentialsForm implements CredentialsFormInterface
{
    public function addFieldsToForm(Form $form)
    {
        $form->addText('user', 'rabbitmq.form.user.label')
            ->setAttribute('placeholder', 'guest')
            ->setAttribute('autofocus');
        $form->addPassword('password', 'rabbitmq.form.password.label')
            ->setAttribute('placeholder', 'guest');
        $form->addText('host', 'rabbitmq.form.host.label')
            ->setAttribute('placeholder', 'localhost');
        $form->addText('port', 'rabbitmq.form.port.label')
            ->setAttribute('placeholder', '5672');
        $form->addText('api_host', 'rabbitmq.form.rabbitmq_management_http_api_host.label')
            ->setAttribute('placeholder', 'localhost')
            ->setOption('description', 'rabbitmq.form.rabbitmq_management_http_api_host.description');
        $form->addText('api_port', 'rabbitmq.form.rabbitmq_management_http_api_port.label')
            ->setAttribute('placeholder', '15672')
            ->setOption('description', 'rabbitmq.form.rabbitmq_management_http_api_port.description');
    }
}
