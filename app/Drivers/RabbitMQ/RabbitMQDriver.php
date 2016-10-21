<?php

namespace Adminerng\Drivers\RabbitMQ;

use Adminerng\Core\AbstractDriver;
use Adminerng\Drivers\RabbitMQ\Forms\RabbitMQCredentialsForm;

class RabbitMQDriver extends AbstractDriver
{
    const TYPE_QUEUE = 'queue';

    private $credentials = [];

    private $client;

    public function extensions()
    {
        return ['curl'];
    }

    public function classes()
    {
        return ['PhpAmqpLib\Connection\AMQPStreamConnection'];
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

    public function getCredentialsForm()
    {
        return new RabbitMQCredentialsForm();
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

    protected function getFormManager()
    {
        return new RabbitMQFormManager($this->dataManager());
    }

    protected function getHeaderManager()
    {
        return new RabbitMQHeaderManager();
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
