<?php

namespace Adminerng\Drivers\Rabbit;

use Adminerng\Core\AbstractDriver;

class RabbitDriver extends AbstractDriver
{
    public function check()
    {
        return true;
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
        return [];
    }
    
    public function connect(array $credentials)
    {
        echo 'con rab';
    }
    
    public function databaseTitle()
    {
        return 'vhost';
    }

    public function databasesHeaders()
    {
    }

    public function databases()
    {
        
    }

    public function selectDatabase($database)
    {
        
    }

    public function tablesHeaders()
    {
        return [];
    }

    public function tables($database)
    {
        
    }

    public function itemsTitles()
    {
        return [
        ];
    }
    
    public function itemsHeaders()
    {
        return [];
    }
    
    public function items($database, $type, $table)
    {
        return [];
    }
    
    protected function getCredentialsForm()
    {
        return new RabbitForm();
    }
}
