<?php

namespace Adminerng\Drivers\RabbitMQ;

use Adminerng\Core\AbstractDriver;
use Adminerng\Core\Column;
use Adminerng\Drivers\RabbitMQ\Forms\RabbitMQMessageForm;
use Adminerng\Drivers\RabbitMQ\Forms\RabbitMQQueueForm;

class RabbitMQDriver extends AbstractDriver
{
    const TYPE_QUEUE = 'queue';

    private $credentials = [];

    private $client;

    public function check()
    {
        return extension_loaded('curl') && class_exists('PhpAmqpLib\Connection\AMQPStreamConnection');
    }

    public function type()
    {
        return 'rabbitmq';
    }

    public function defaultCredentials()
    {
        return [
            'host' => 'localhost',
            'port' => '5672',
            'api_host' => 'localhost',
            'api_port' => '15672',
            'user' => 'guest',
            'password' => 'guest',
        ];
    }

    public function connect(array $credentials)
    {
        $this->credentials = $credentials;

        $this->client = new RabbitMQManagementApiClient(
            $this->credentials['api_host'],
            $this->credentials['api_port'],
            $this->credentials['user'],
            $this->credentials['password']
        );
        $this->client->overview();
    }

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
            ->setIsNumeric(true);
        return [
            self::TYPE_QUEUE => $columns,
        ];
    }

    public function columns($type, $table)
    {
        $columns = [];
        if ($type == self::TYPE_QUEUE) {
            $columns[] = (new Column())
                ->setKey('message_body')
                ->setTitle('rabbitmq.columns.' . $type . '.message_body');
            $columns[] = (new Column())
                ->setKey('size')
                ->setTitle('rabbitmq.columns.' . $type . '.size');
            $columns[] = (new Column())
                ->setKey('is_truncated')
                ->setTitle('rabbitmq.columns.' . $type . '.is_truncated');
            $columns[] = (new Column())
                ->setKey('content_encoding')
                ->setTitle('rabbitmq.columns.' . $type . '.content_encoding');
            $columns[] = (new Column())
                ->setKey('redelivered')
                ->setTitle('rabbitmq.columns.' . $type . '.redelivered');
        }
        return $columns;
    }

    protected function getCredentialsForm()
    {
        return new RabbitMQForm();
    }

    public function tableForm($database, $type, $table)
    {
        $connection = $this->dataManager()->getConnection();
        if ($type === self::TYPE_QUEUE) {
            return new RabbitMQQueueForm($connection, $table);
        }
        return parent::tableForm($database, $type, $table);
    }

    public function itemForm($database, $type, $table, $item)
    {
        $connection = $this->dataManager()->getConnection();
        if (!$item && $type === self::TYPE_QUEUE) {
            return new RabbitMQMessageForm($connection, $table);
        }
        parent::itemForm($database, $type, $table, $item);
    }

    protected function getPermissions()
    {
        return new RabbitMQPermissions();
    }

    protected function getDataManager()
    {
        return new RabbitMQDataManager($this->credentials, $this->client, $this->translator, $this->formatter);
    }
}
