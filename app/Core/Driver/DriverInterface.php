<?php

namespace Adminerng\Core\Driver;

use Adminerng\Core\CredentialsFormInterface;
use Adminerng\Core\DataManager\DataManagerInterface;
use Adminerng\Core\Forms\FormManagerInterface;
use Adminerng\Core\ListingHeaders\HeaderManagerInterface;
use Adminerng\Core\Permissions\PermissionsInterface;

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
