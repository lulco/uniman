<?php

namespace UniMan\Drivers\RabbitMQ;

use UniMan\Core\Column;
use UniMan\Core\ListingHeaders\HeaderManagerInterface;

class RabbitMQHeaderManager implements HeaderManagerInterface
{
    public function databasesHeaders()
    {
        $columns = [];
        $columns[] = (new Column('vhost', 'rabbitmq.headers.vhosts.vhost'))
            ->setSortable(true);
        $columns[] = (new Column('queues', 'rabbitmq.headers.vhosts.queues'))
            ->setSortable(true)
            ->setNumeric(true);
        $columns[] = (new Column('messages', 'rabbitmq.headers.vhosts.messages'))
            ->setSortable(true)
            ->setNumeric(true);
        return $columns;
    }

    public function tablesHeaders()
    {
        $columns = [];
        $columns[] = (new Column('queue', 'rabbitmq.headers.queues.queue'))
            ->setSortable(true);
        $columns[] = (new Column('number_of_items', 'rabbitmq.headers.queues.number_of_items'))
            ->setSortable(true)
            ->setNumeric(true);
        $columns[] = (new Column('size', 'rabbitmq.headers.queues.size'))
            ->setSortable(true)
            ->setNumeric(true)
            ->setSize(true);
        return [
            RabbitMQDriver::TYPE_QUEUE => $columns,
        ];
    }

    public function itemsHeaders($type, $table)
    {
        $columns = [];
        if ($type == RabbitMQDriver::TYPE_QUEUE) {
            $columns[] = (new Column('message_body', 'rabbitmq.columns.' . $type . '.message_body'))
                ->setSortable(true)
                ->setFilterable(true);
            $columns[] = (new Column('length', 'rabbitmq.columns.' . $type . '.length'))
                ->setNumeric(true)
                ->setSortable(true)
                ->setFilterable(true);
            $columns[] = (new Column('is_truncated', 'rabbitmq.columns.' . $type . '.is_truncated'))
                ->setSortable(true)
                ->setFilterable(true);
            $columns[] = (new Column('content_encoding', 'rabbitmq.columns.' . $type . '.content_encoding'))
                ->setSortable(true)
                ->setFilterable(true);
            $columns[] = (new Column('redelivered', 'rabbitmq.columns.' . $type . '.redelivered'))
                ->setSortable(true)
                ->setFilterable(true);
        }
        return $columns;
    }
}
