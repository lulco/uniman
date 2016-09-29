<?php

namespace Adminerng\Drivers\Rabbit\Forms;

use Adminerng\Core\Forms\ItemForm\ItemFormInterface;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitQueueForm implements ItemFormInterface
{
    private $connection;

    private $queue;

    public function __construct(AMQPStreamConnection $connection, $queue = null)
    {
        $this->connection = $connection;
        $this->queue = $queue;
    }

    public function addFieldsToForm(Form $form)
    {
        $form->addText('queue', 'rabbit.queue_form.queue.label')
            ->setRequired('rabbit.queue_form.queue.required');
    }

    public function submit(Form $form, ArrayHash $values)
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($values['queue'], false, false, false, false);
    }
}
