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

    public final function addFormFields(Form $form)
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
    public final function permissions()
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
