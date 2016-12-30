<?php

namespace Adminerng\Drivers\RabbitMQ;

use Adminerng\Core\Column;
use Adminerng\Core\ListingHeaders\HeaderManagerInterface;

class RabbitMQHeaderManager implements HeaderManagerInterface
{
    public function databasesHeaders()
    {
        $columns = [];
        $columns[] = (new Column('vhost', 'rabbitmq.headers.vhosts.vhost'))
            ->setIsSortable(true);
        $columns[] = (new Column('queues', 'rabbitmq.headers.vhosts.queues'))
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column('messages', 'rabbitmq.headers.vhosts.messages'))
            ->setIsSortable(true)
            ->setIsNumeric(true);
        return $columns;
    }

    public function tablesHeaders()
    {
        $columns = [];
        $columns[] = (new Column('queue', 'rabbitmq.headers.queues.queue'))
            ->setIsSortable(true);
        $columns[] = (new Column('number_of_items', 'rabbitmq.headers.queues.number_of_items'))
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column('size', 'rabbitmq.headers.queues.size'))
            ->setIsSortable(true)
            ->setIsNumeric(true)
            ->setIsSize(true);
        return [
            RabbitMQDriver::TYPE_QUEUE => $columns,
        ];
    }

    public function itemsHeaders($type, $table)
    {
        $columns = [];
        if ($type == RabbitMQDriver::TYPE_QUEUE) {
            $columns[] = (new Column('message_body', 'rabbitmq.columns.' . $type . '.message_body'))
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column('length', 'rabbitmq.columns.' . $type . '.length'))
                ->setIsNumeric(true)
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column('is_truncated', 'rabbitmq.columns.' . $type . '.is_truncated'))
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column('content_encoding', 'rabbitmq.columns.' . $type . '.content_encoding'))
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column('redelivered', 'rabbitmq.columns.' . $type . '.redelivered'))
                ->setIsSortable(true)
                ->setIsFilterable(true);
        }
        return $columns;
    }
}
