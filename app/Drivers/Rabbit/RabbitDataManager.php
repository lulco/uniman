<?php

namespace Adminerng\Drivers\Rabbit;

use Adminerng\Core\DataManagerInterface;
use Nette\Localization\ITranslator;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitDataManager implements DataManagerInterface
{
    private $translator;

    /** @var AMQPStreamConnection */
    private $connection;

    private $credentials = [];

    private $client;

    public function __construct(array $credentials, RabbitManagementApiClient $client, ITranslator $translator)
    {
        $this->credentials = $credentials;
        $this->client = $client;
        $this->translator = $translator;
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
        return $this->connection;
    }

    public function tables($database)
    {
        $tables = [
            RabbitDriver::TYPE_QUEUE => [],
        ];
        foreach ($this->client->getQueues($database) as $queue) {
            $tables[RabbitDriver::TYPE_QUEUE][$queue['name']] = [
                'items' => $queue['messages'],
                'size' => $queue['message_bytes'],
            ];
        }
        return $tables;
    }

    public function itemsCount($database, $type, $table, array $filter = [])
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

    public function items($database, $type, $table, $page, $onPage, array $filter = [], array $sorting = [])
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
                'is_truncated' => $message->isTruncated() ? $this->translator->translate('core.yes') : $this->translator->translate('core.no'),
                'content_encoding' => $message->getContentEncoding(),
                'redelivered' => $message->get('redelivered') ? $this->translator->translate('core.yes') : $this->translator->translate('core.no'),
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

    public function deleteTable($vhost, $type, $queue)
    {
        $this->selectDatabase($vhost);
        $channel = $this->connection->channel();
        $res = $channel->queue_delete($queue);
        return true;
    }

    private function getMessage($queue)
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($queue, false, false, false, false);
        return $channel->basic_get($queue);
    }
}
