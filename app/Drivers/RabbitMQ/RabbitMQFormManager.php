<?php

namespace Adminerng\Drivers\RabbitMQ;

use Adminerng\Core\Forms\DefaultFormManager;
use Adminerng\Drivers\RabbitMQ\Forms\RabbitMQMessageForm;
use Adminerng\Drivers\RabbitMQ\Forms\RabbitMQQueueForm;

class RabbitMQFormManager extends DefaultFormManager
{
    private $dataManager;

    public function __construct(RabbitMQDataManager $dataManager)
    {
        $this->dataManager = $dataManager;
    }

    public function itemForm($database, $type, $table, $item)
    {
        $connection = $this->dataManager->getConnection();
        if (!$item && $type === RabbitMQDriver::TYPE_QUEUE) {
            return new RabbitMQMessageForm($connection, $table);
        }
        parent::itemForm($database, $type, $table, $item);
    }

    public function tableForm($database, $type, $table)
    {
        $connection = $this->dataManager->getConnection();
        if ($type === RabbitMQDriver::TYPE_QUEUE) {
            return new RabbitMQQueueForm($connection, $table);
        }
        return parent::tableForm($database, $type, $table);
    }
}
