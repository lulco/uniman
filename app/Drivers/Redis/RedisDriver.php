<?php

namespace UniMan\Drivers\Redis;

use Nette\Localization\ITranslator;
use RedisException;
use RedisProxy\RedisProxy;
use UniMan\Core\Driver\AbstractDriver;
use UniMan\Core\Driver\AbstractDriver;
use UniMan\Core\Exception\ConnectException;
use UniMan\Core\Exception\ConnectException;
use UniMan\Drivers\Redis\Forms\RedisCredentialsForm;
use UniMan\Drivers\Redis\Forms\RedisCredentialsForm;
use UniMan\Drivers\Redis\RedisDatabaseAliasStorage;

class RedisDriver extends AbstractDriver
{
    const TYPE_KEY = 'key';
    const TYPE_HASH = 'hash';
    const TYPE_SET = 'set';

    private $connection;

    private $databaseAliasStorage;

    public function __construct(ITranslator $translator, RedisDatabaseAliasStorage $databaseAliasStorage)
    {
        parent::__construct($translator);
        $this->databaseAliasStorage = $databaseAliasStorage;
    }

    public function check()
    {
        return extension_loaded('redis') || class_exists('Predis\Client');
    }

    public function extensions()
    {
        return ['redis'];
    }

    public function classes()
    {
        return ['Predis\Client'];
    }

    public function type()
    {
        return 'redis';
    }

    public function defaultCredentials()
    {
        return [
            'host' => 'localhost',
            'port' => '6379',
            'database' => '0',
        ];
    }

    public function getCredentialsForm()
    {
        return new RedisCredentialsForm();
    }

    public function connect(array $credentials)
    {
        try {
            $this->connection = new RedisProxy($credentials['host'], $credentials['port'], $credentials['database']);
        } catch (RedisException $e) {
            throw new ConnectException($e->getMessage());
        }
    }

    protected function getFormManager()
    {
        return new RedisFormManager($this->connection, $this->databaseAliasStorage);
    }

    protected function getHeaderManager()
    {
        return new RedisHeaderManager();
    }

    protected function getPermissions()
    {
        return new RedisPermissions();
    }

    protected function getDataManager()
    {
        return new RedisDataManager($this->connection, $this->databaseAliasStorage);
    }
}
