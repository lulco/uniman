<?php

namespace Adminerng\Drivers\RabbitMQ\Forms;

use Adminerng\Core\Forms\ItemForm\ItemFormInterface;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQQueueForm implements ItemFormInterface
{
    private $connection;

    public function __construct(AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
    }

    public function addFieldsToForm(Form $form)
    {
        $form->addText('queue', 'rabbitmq.queue_form.queue.label')
            ->setRequired('rabbitmq.queue_form.queue.required');
    }

    public function submit(Form $form, ArrayHash $values)
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($values['queue'], false, false, false, false);
        $channel->close();
        $this->connection->close();
    }
}
