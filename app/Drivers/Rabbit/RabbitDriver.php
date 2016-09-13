<?php

namespace Adminerng\Drivers\Rabbit;

use Adminerng\Core\AbstractDriver;

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

    public function tablesHeaders()
    {
        return [
            self::TYPE_QUEUE => ['Queue', 'Number of items', 'Size']
        ];
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

    protected function getCredentialsForm()
    {
        return new RabbitForm();
    }

    protected function getDataManager()
    {
        return new RabbitDataManager($this->credentials, $this->client);
    }
}
