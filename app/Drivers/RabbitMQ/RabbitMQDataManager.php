<?php

namespace Adminerng\Drivers\RabbitMQ;

use Adminerng\Core\DataManager\AbstractDataManager;
use Adminerng\Core\Helper\Formatter;
use Adminerng\Core\Multisort;
use Adminerng\Core\Utils\Filter;
use Nette\Localization\ITranslator;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQDataManager extends AbstractDataManager
{
    private $translator;

    private $formatter;

    /** @var AMQPStreamConnection */
    private $connection;

    private $credentials = [];

    private $client;

    private $vhost;

    public function __construct(array $credentials, RabbitMQManagementApiClient $client, ITranslator $translator, Formatter $formatter)
    {
        $this->credentials = $credentials;
        $this->client = $client;
        $this->translator = $translator;
        $this->formatter = $formatter;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function databases(array $sorting = [])
    {
        $vhosts = [];
        foreach ($this->client->getVhosts($this->credentials['user']) as $vhost) {
            $vhosts[$vhost['name']] = [
                'vhost' => $vhost['name'],
                'queues' => count($this->client->getQueues($vhost['name'])),
                'messages' => isset($vhost['messages']) ? $vhost['messages'] : 0,
            ];
        }
        return Multisort::sort($vhosts, $sorting);
    }

    public function selectDatabase($vhost)
    {
        $this->vhost = $vhost;
        $this->connection = new AMQPStreamConnection(
            $this->credentials['host'],
            $this->credentials['port'],
            $this->credentials['user'],
            $this->credentials['password'],
            $vhost
        );
        return $this->connection;
    }

    public function tables(array $sorting = [])
    {
        $tables = [
            RabbitMQDriver::TYPE_QUEUE => [],
        ];
        foreach ($this->client->getQueues($this->vhost) as $queue) {
            $tables[RabbitMQDriver::TYPE_QUEUE][$queue['name']] = [
                'queue' => $queue['name'],
                'number_of_items' => $queue['messages'],
                'size' => $queue['message_bytes'],
            ];
        }
        return [
            RabbitMQDriver::TYPE_QUEUE => Multisort::sort($tables[RabbitMQDriver::TYPE_QUEUE], $sorting),
        ];
    }

    public function itemsCount($type, $table, array $filter = [])
    {
        if ($type != RabbitMQDriver::TYPE_QUEUE) {
            return 0;
        }
        if (!$filter) {
            foreach ($this->client->getQueues($this->vhost) as $queue) {
                if ($queue['name'] == $table) {
                    return $queue['messages'];
                }
            }
        }
        $totalCount = 0;
        foreach ($this->client->getMessages($this->vhost, $table) as $message) {
            $item = [
                'message_body' => $message['payload'],
                'length' => $message['payload_bytes'],
                'is_truncated' => false ? $this->translator->translate('core.yes') : $this->translator->translate('core.no'),
                'content_encoding' => $message['payload_encoding'],
                'redelivered' => $message['redelivered'] ? $this->translator->translate('core.yes') : $this->translator->translate('core.no'),
            ];
            if (Filter::apply($item, $filter)) {
                $totalCount++;
            }
        }

        return $totalCount;
    }

    public function items($type, $table, $page, $onPage, array $filter = [], array $sorting = [])
    {
        if ($type != RabbitMQDriver::TYPE_QUEUE) {
            return [];
        }
        $items = [];
        foreach ($this->client->getMessages($this->vhost, $table) as $message) {
            $item = [
                'message_body' => $message['payload'],
                'length' => $message['payload_bytes'],
                'is_truncated' => false ? $this->translator->translate('core.yes') : $this->translator->translate('core.no'),
                'content_encoding' => $message['payload_encoding'],
                'redelivered' => $message['redelivered'] ? $this->translator->translate('core.yes') : $this->translator->translate('core.no'),
            ];
            if (!Filter::apply($item, $filter)) {
                continue;
            }
            $items[$message['payload']] = $item;
        }
        $items = Multisort::sort($items, $sorting);
        return array_slice($items, ($page - 1) * $onPage, $onPage, true);
    }

    public function deleteTable($type, $queue)
    {
        $channel = $this->connection->channel();
        $channel->queue_delete($queue);
        $channel->close();
        $this->connection->close();
        return true;
    }

    private function getMessage($queue)
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($queue, false, false, false, false);
        return $channel->basic_get($queue);
    }
}
