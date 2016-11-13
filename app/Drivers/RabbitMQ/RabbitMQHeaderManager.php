<?php

namespace Adminerng\Drivers\RabbitMQ;

use Adminerng\Core\Column;
use Adminerng\Core\ListingHeaders\HeaderManagerInterface;

class RabbitMQHeaderManager implements HeaderManagerInterface
{
    public function databasesHeaders()
    {
        $columns = [];
        $columns[] = (new Column())
            ->setKey('vhost')
            ->setTitle('rabbitmq.headers.vhosts.vhost')
            ->setIsSortable(true);
        $columns[] = (new Column())
            ->setKey('queues')
            ->setTitle('rabbitmq.headers.vhosts.queues')
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column())
            ->setKey('messages')
            ->setTitle('rabbitmq.headers.vhosts.messages')
            ->setIsSortable(true)
            ->setIsNumeric(true);
        return $columns;
    }

    public function tablesHeaders()
    {
        $columns = [];
        $columns[] = (new Column())
            ->setKey('queue')
            ->setTitle('rabbitmq.headers.queues.queue')
            ->setIsSortable(true);
        $columns[] = (new Column())
            ->setKey('number_of_items')
            ->setTitle('rabbitmq.headers.queues.number_of_items')
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column())
            ->setKey('size')
            ->setTitle('rabbitmq.headers.queues.size')
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
            $columns[] = (new Column())
                ->setKey('message_body')
                ->setTitle('rabbitmq.columns.' . $type . '.message_body')
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column())
                ->setKey('length')
                ->setTitle('rabbitmq.columns.' . $type . '.length')
                ->setIsNumeric(true)
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column())
                ->setKey('is_truncated')
                ->setTitle('rabbitmq.columns.' . $type . '.is_truncated')
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column())
                ->setKey('content_encoding')
                ->setTitle('rabbitmq.columns.' . $type . '.content_encoding')
                ->setIsSortable(true)
                ->setIsFilterable(true);
            $columns[] = (new Column())
                ->setKey('redelivered')
                ->setTitle('rabbitmq.columns.' . $type . '.redelivered')
                ->setIsSortable(true)
                ->setIsFilterable(true);
        }
        return $columns;
    }
}
