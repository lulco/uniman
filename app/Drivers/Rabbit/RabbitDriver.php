<?php

namespace Adminerng\Drivers\Rabbit;

use Adminerng\Core\AbstractDriver;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitDriver extends AbstractDriver
{
    private $credentials = [];

    private $client;

    public function check()
    {
        return class_exists('\PhpAmqpLib\Connection\AMQPStreamConnection');
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

    public function databaseTitle()
    {
        return 'vhost';
    }

    public function databasesHeaders()
    {
        return [
            'Vhost', 'Messages'
        ];
    }

    public function databases()
    {
        $vhosts = [];
        foreach ($this->client->getVhosts($this->credentials['user']) as $vhost) {
            $vhosts[$vhost['name']] = [
                'messages' => isset($vhost['messages']) ? $vhost['messages'] : 0,
            ];
        }
        return $vhosts;
    }

    private function connectToVhost($vhost)
    {
        $this->connection = new AMQPStreamConnection(
            $this->credentials['host'],
            $this->credentials['port'],
            $this->credentials['user'],
            $this->credentials['password'],
            $vhost
        );
    }

    public function tablesHeaders()
    {
        return [
            'Queues' => ['Queue', 'Number of items', 'Size']
        ];
    }

    public function tables($database)
    {
        $tables = [];
        foreach ($this->client->getQueues($database) as $queue) {
            $tables['Queues'][$queue['name']] = [
                'items' => $queue['messages'],
                'size' => $queue['message_bytes'],
            ];
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
    
    public function itemsCount($database, $type, $table)
    {
        return 1000;
    }
    
    public function items($database, $type, $table, $page, $onPage)
    {
        $this->connectToVhost($database);
        $items = [];
        while($message = $this->getMessage($table)) {
            $items[$message->getBody()] = [
                'message_body' => $message->getBody(),
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
    
    private function getMessage($queue)
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($queue, false, false, false, false);
        return $channel->basic_get($queue);
    }
}
