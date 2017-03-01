<?php

namespace UniMan\Drivers\RabbitMQ;

use UniMan\Core\Forms\DefaultFormManager;
use UniMan\Drivers\RabbitMQ\Forms\RabbitMQMessageForm;
use UniMan\Drivers\RabbitMQ\Forms\RabbitMQQueueForm;

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
            return new RabbitMQQueueForm($connection);
        }
        return parent::tableForm($database, $type, $table);
    }
}
