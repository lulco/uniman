<?php

namespace UniMan\Core\Driver;

use UniMan\Core\CredentialsFormInterface;
use UniMan\Core\DataManager\DataManagerInterface;
use UniMan\Core\Forms\FormManagerInterface;
use UniMan\Core\ListingHeaders\HeaderManagerInterface;
use UniMan\Core\Permissions\PermissionsInterface;

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
     * @return CredentialsFormInterface
     */
    public function getCredentialsForm();

    /**
     * @return array default credentials for connect
     */
    public function defaultCredentials();

    /**
     * creates connection for driver
     * @param array $credentials
     */
    public function connect(array $credentials);

    /**
     * @return PermissionsInterface
     */
    public function permissions();

    /**
     * @return DataManagerInterface
     */
    public function dataManager();

    /**
     * @return FormManagerInterface
     */
    public function formManager();

    /**
     * @return HeaderManagerInterface
     */
    public function headerManager();
}
