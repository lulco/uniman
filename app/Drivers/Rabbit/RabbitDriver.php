<?php

namespace Adminerng\Drivers\Rabbit;

use Adminerng\Core\AbstractDriver;
use Adminerng\Core\Column;

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
        return [
            'Vhost', 'Messages'
        ];
    }

    public function tablesHeaders()
    {
        return [
            self::TYPE_QUEUE => ['Queue', 'Number of items', 'Size']
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

    protected function getDataManager()
    {
        return new RabbitDataManager($this->credentials, $this->client, $this->translator);
    }
}
