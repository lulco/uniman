<?php

namespace Adminerng\Drivers\Rabbit;

use Adminerng\Core\AbstractDriver;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitDriver extends AbstractDriver
{
    private $vhost;

    private $queues = [];

    public function check()
    {
        return class_exists('\PhpAmqpLib\Connection\AMQPStreamConnection');
    }

    public function type()
    {
        return 'rabbit';
    }

    public function name()
    {
        return 'Rabbit';
    }

    public function defaultCredentials()
    {
        return [
            'host' => 'localhost',
            'port' => '5672',
            'user' => 'guest',
            'password' => 'guest',
            'vhost' => '/',
        ];
    }

    public function connect(array $credentials)
    {
        $this->vhost = $credentials['vhost'];
        $this->queues = array_map('trim', explode("\n", $credentials['queues']));

        $this->connection = new AMQPStreamConnection(
            $credentials['host'],
            $credentials['port'],
            $credentials['user'],
            $credentials['password'],
            $credentials['vhost']
        );
    }
    
    public function databaseTitle()
    {
        return 'vhost';
    }

    public function databasesHeaders()
    {
        return [
            'Vhost'
        ];
    }

    public function databases()
    {
        return [
            $this->vhost => [],
        ];
    }

    public function selectDatabase($database)
    {
    }

    public function tablesHeaders()
    {
        return [
            'Queues' => ['Queue']
        ];
    }

    public function tables($database)
    {
        $tables = [];
        foreach ($this->queues as $queue) {
            $tables['Queues'][$queue] = [];
        }
        return $tables;
    }

    public function itemsTitles()
    {
        return [
            'Queues' => 'Message'
        ];
    }
    
    public function itemsHeaders()
    {
        return [
            'Queues' => ['Message body', 'Size', 'Is truncated', 'Content encoding', 'Redelivered']
        ];
    }
    
    public function items($database, $type, $table)
    {
        $channel = $this->connection->channel();
        $items = [];
        while($message = $channel->basic_get($table)) {
            $items[$message->getBody()] = [
                'size' => $message->getBodySize(),
                'is_truncated' => $message->isTruncated() ? 'Yes' : 'No',
                'content_encoding' => $message->getContentEncoding(),
                'redelivered' => $message->get('redelivered') ? 'Yes' : 'No',
            ];
        }
        return $items;
    }
    
    protected function getCredentialsForm()
    {
        return new RabbitForm();
    }
}
