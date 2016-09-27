<?php

namespace Adminerng\Core;

use Adminerng\Core\Forms\ItemForm\ItemFormInterface;
use Adminerng\Core\Forms\TableForm\TableFormInterface;
use Adminerng\Core\Permissions\PermissionsInterface;
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

    /**
     * @return array table column names
     */
    public function databasesHeaders();

    public function tablesHeaders();

    public function columns($type, $table);

    /**
     * adds fields to credential form
     * @param Form $form
     */
    public function addFormFields(Form $form);

    /**
     * create / edit item form
     * @param string $database
     * @param string $type
     * @param string $table
     * @param mixed|null $item is null if create item form is rendered
     * @return ItemFormInterface
     */
    public function itemForm($database, $type, $table, $item);

    /**
     * create / edit table form
     * @param string $database
     * @param string $type
     * @param string|null $table
     * @return TableFormInterface
     */
    public function tableForm($database, $type, $table);

    /**
     * @return PermissionsInterface
     */
    public function permissions();

    /**
     * @return DataManagerInterface
     */
    public function dataManager();
}
