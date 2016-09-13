<?php

namespace Adminerng\Drivers\Rabbit;

use Adminerng\Core\DataManagerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitDataManager implements DataManagerInterface
{
    private $connection;

    private $credentials = [];

    private $client;

    public function __construct(array $credentials, RabbitManagementApiClient $client)
    {
        $this->credentials = $credentials;
        $this->client = $client;
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

    public function selectDatabase($vhost)
    {
        $this->connection = new AMQPStreamConnection(
            $this->credentials['host'],
            $this->credentials['port'],
            $this->credentials['user'],
            $this->credentials['password'],
            $vhost
        );
    }

    public function tables($database)
    {
        $tables = [];
        foreach ($this->client->getQueues($database) as $queue) {
            $tables[RabbitDriver::TYPE_QUEUE][$queue['name']] = [
                'items' => $queue['messages'],
                'size' => $queue['message_bytes'],
            ];
        }
        return $tables;
    }

    public function itemsCount($database, $type, $table)
    {
        if ($type != RabbitDriver::TYPE_QUEUE) {
            return 0;
        }
        foreach ($this->client->getQueues($database) as $queue) {
            if ($queue['name'] == $table) {
                return $queue['messages'];
            }
        }
        return 0;
    }

    public function items($database, $type, $table, $page, $onPage)
    {
        if ($type != RabbitDriver::TYPE_QUEUE) {
            return [];
        }
        $this->selectDatabase($database);
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

    public function deleteItem($database, $type, $table, $item)
    {
        return false;
    }

    private function getMessage($queue)
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($queue, false, false, false, false);
        return $channel->basic_get($queue);
    }
}
