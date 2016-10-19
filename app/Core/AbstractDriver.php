<?php

namespace Adminerng\Core;

use Adminerng\Core\Helper\Formatter;
use Adminerng\Core\Permissions\DefaultPermissions;
use Adminerng\Core\Permissions\PermissionsInterface;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;

abstract class AbstractDriver implements DriverInterface
{
    protected $connection;

    protected $translator;

    protected $formatter;

    private $permissions;

    private $dataManager;

    public function __construct(ITranslator $translator, Formatter $formatter)
    {
        $this->translator = $translator;
        $this->formatter = $formatter;
    }

    public function name()
    {
        return $this->type() . '.name';
    }

    /**
     * check if driver can be used
     * @return boolean
     */
    public function check()
    {
        foreach ($this->extensions() as $extension) {
            if (!extension_loaded($extension)) {
                return false;
            }
        }
        foreach ($this->classes() as $class) {
            if (!class_exists($class)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return array list of php extensions which should be loaded
     */
    abstract public function extensions();

    /**
     *
     * @return array list of php classes which should exist
     */
    public function classes()
    {
        return [];
    }


    final public function addFormFields(Form $form)
    {
        return $this->getCredentialsForm()->addFieldsToForm($form);
    }

    public function itemForm($database, $type, $table, $item)
    {
        return false;
    }

    public function tableForm($database, $type, $table)
    {
        return false;
    }

    /**
     * @return CredentialsFormInterface
     */
    abstract protected function getCredentialsForm();

    /**
     * @return PermissionsInterface
     */
    final public function permissions()
    {
        if ($this->permissions === null) {
            $this->permissions = $this->getPermissions();
        }
        return $this->permissions;
    }

    /**
     * can be overriden in child
     * @return PermissionsInterface
     */
    protected function getPermissions()
    {
        return new DefaultPermissions();
    }

    /**
     * @return DataManagerInterface
     */
    public function dataManager()
    {
        if ($this->dataManager === null) {
            $this->dataManager = $this->getDataManager();
        }
        return $this->dataManager;
    }

    /**
     * @return DataManagerInterface
     */
    abstract protected function getDataManager();
}
