<?php

namespace UniMan\Drivers\RabbitMQ\Forms;

use UniMan\Core\Forms\ItemForm\ItemFormInterface;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQMessageForm implements ItemFormInterface
{
    private $connection;

    private $queue;

    public function __construct(AMQPStreamConnection $connection, $queue)
    {
        $this->connection = $connection;
        $this->queue = $queue;
    }

    public function addFieldsToForm(Form $form)
    {
        $form->addText('message', 'rabbitmq.message_form.message.label')
            ->setRequired('rabbitmq.message_form.message.required');
    }

    public function submit(Form $form, ArrayHash $values)
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($this->queue, false, false, false, false);
        $messsage = new AMQPMessage($values['message']);
        $channel->basic_publish($messsage, '', $this->queue);
        $channel->close();
        $this->connection->close();
    }
}
