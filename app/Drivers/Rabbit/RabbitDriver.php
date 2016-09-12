<?php

namespace Adminerng\Drivers\Rabbit;

use Adminerng\Core\AbstractDriver;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitDriver extends AbstractDriver
{
    const TYPE_QUEUE = 'queue';

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
            self::TYPE_QUEUE => ['Queue', 'Number of items', 'Size']
        ];
    }

    public function tables($database)
    {
        $tables = [];
        foreach ($this->client->getQueues($database) as $queue) {
            $tables[self::TYPE_QUEUE][$queue['name']] = [
                'items' => $queue['messages'],
                'size' => $queue['message_bytes'],
            ];
        }
        return $tables;
    }

    public function itemsTitles($type = null)
    {
        $titles = [
            self::TYPE_QUEUE => 'Message'
        ];
        return $type === null ? $titles : $titles[$type];
    }
    
    public function itemsHeaders($type, $table)
    {
        $headers = [
            self::TYPE_QUEUE => ['Message body', 'Size', 'Is truncated', 'Content encoding', 'Redelivered']
        ];
        return isset($headers[$type]) ? $headers[$type] : [];
    }
    
    public function itemsCount($database, $type, $table)
    {
        foreach ($this->client->getQueues($database) as $queue) {
            if ($queue['name'] == $table) {
                return $queue['messages'];
            }
        }
        return 0;
    }
    
    public function items($database, $type, $table, $page, $onPage)
    {
        $this->connectToVhost($database);
        $items = [];
        while ($message = $this->getMessage($table)) {
            $items[$message->getBody()] = [
                'message_body' => $message->getBody(),
                'size' => $message->getBodySize(),
                'is_truncated' => $message->isTruncated() ? 'Yes' : 'No',
                'content_encoding' => $message->getContentEncoding(),
                'redelivered' => $message->get('redelivered') ? 'Yes' : 'No',
            ];
            if (count($items) == $page * $onPage) {
                break;
            }
        }
        return array_slice($items, ($page - 1) * $onPage, $onPage, true);
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

    public function dataManager()
    {
        
    }

}
