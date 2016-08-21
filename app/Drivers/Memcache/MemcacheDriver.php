<?php

namespace Adminerng\Drivers\Memcache;

use Adminerng\Core\DriverInterface;
use Nette\Application\UI\Form;

class MemcacheDriver implements DriverInterface
{
    public function check()
    {
        return true;
    }

    public function type()
    {
        return 'memcache';
    }

    public function name()
    {
        return 'Memcache';
    }
    
    public function defaultCredentials()
    {
        return [];
    }
    
    public function connect(array $credentials)
    {
        echo 'con mem';
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
    
    public function itemsHeaders()
    {
        return [];
    }
    
    public function items($database, $type, $table)
    {
        return [];
    }

    public function addFormFields(Form $form)
    {
        
    }
}
