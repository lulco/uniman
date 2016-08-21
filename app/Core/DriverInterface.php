<?php

namespace Adminerng\Core;

use Nette\Application\UI\Form;

interface DriverInterface
{
    /**
     * @return string type of driver
     */
    public function type();

    /**
     * @return string name of driver
     */
    public function name();
    
    /**
     * checks if required php extensions and/or php libraries are available
     * @return boolean
     */
    public function check();
    
    /**
     * @return array default credentials for connect
     */
    public function defaultCredentials();

    /**
     * creates connection for driver
     * @param array $credentials
     */
    public function connect(array $credentials);

    public function databaseTitle();
    
    /**
     * @return array table column names
     */
    public function databasesHeaders();
    
    // database, vhost - vyymsliet nazov spolocny
    public function databases();
    
    public function selectDatabase($database);
    
    public function tablesHeaders();

    // storages - alebo nejaky vseobecny nazov pre db: tables, redis: hashsets, keys, memcache: key, rabbit: queues
    public function tables($database);
    
    public function itemsTitles();

    public function itemsHeaders();
    
    public function items($database, $type, $table);
    /**
     * adds fields to credential form
     * @param Form $form
     */
    public function addFormFields(Form $form);
}
