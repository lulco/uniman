<?php

namespace Adminerng\Drivers\Rabbit;

use Adminerng\Core\AbstractDriver;
use Adminerng\Core\Column;
use Adminerng\Drivers\Rabbit\Forms\RabbitMessageForm;
use Adminerng\Drivers\Rabbit\Forms\RabbitQueueForm;

class RabbitDriver extends AbstractDriver
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
        return 'rabbit';
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

        $this->client = new RabbitManagementApiClient(
            $this->credentials['api_host'],
            $this->credentials['api_port'],
            $this->credentials['user'],
            $this->credentials['password']
        );
    }

    public function databasesHeaders()
    {
        $columns = [];
        $columns[] = (new Column())
            ->setKey('vhost')
            ->setTitle('rabbit.headers.vhosts.vhost')
            ->setIsSortable(true);
        $columns[] = (new Column())
            ->setKey('queues')
            ->setTitle('rabbit.headers.vhosts.queues')
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column())
            ->setKey('messages')
            ->setTitle('rabbit.headers.vhosts.messages')
            ->setIsSortable(true)
            ->setIsNumeric(true);
        return $columns;
    }

    public function tablesHeaders()
    {
        $columns = [];
        $columns[] = (new Column())
            ->setKey('queue')
            ->setTitle('rabbit.headers.queues.queue')
            ->setIsSortable(true);
        $columns[] = (new Column())
            ->setKey('number_of_items')
            ->setTitle('rabbit.headers.queues.number_of_items')
            ->setIsSortable(true)
            ->setIsNumeric(true);
        $columns[] = (new Column())
            ->setKey('size')
            ->setTitle('rabbit.headers.queues.size')
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
                ->setTitle('rabbit.columns.' . $type . '.message_body');
            $columns[] = (new Column())
                ->setKey('size')
                ->setTitle('rabbit.columns.' . $type . '.size');
            $columns[] = (new Column())
                ->setKey('is_truncated')
                ->setTitle('rabbit.columns.' . $type . '.is_truncated');
            $columns[] = (new Column())
                ->setKey('content_encoding')
                ->setTitle('rabbit.columns.' . $type . '.content_encoding');
            $columns[] = (new Column())
                ->setKey('redelivered')
                ->setTitle('rabbit.columns.' . $type . '.redelivered');
        }
        return $columns;
    }

    protected function getCredentialsForm()
    {
        return new RabbitForm();
    }

    public function tableForm($database, $type, $table)
    {
        $this->dataManager()->selectDatabase($database);
        $connection = $this->dataManager()->getConnection();
        if ($type === self::TYPE_QUEUE) {
            return new RabbitQueueForm($connection, $table);
        }
        return parent::tableForm($database, $type, $table);
    }

    public function itemForm($database, $type, $table, $item)
    {
        $this->dataManager()->selectDatabase($database);
        $connection = $this->dataManager()->getConnection();
        if (!$item && $type === self::TYPE_QUEUE) {
            return new RabbitMessageForm($connection, $table);
        }
        parent::itemForm($database, $type, $table, $item);
    }

    protected function getPermissions()
    {
        return new RabbitPermissions();
    }

    protected function getDataManager()
    {
        return new RabbitDataManager($this->credentials, $this->client, $this->translator, $this->formatter);
    }
}
