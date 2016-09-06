<?php

namespace Adminerng\Core;

use Adminerng\Core\Permissions\DefaultPermissions;
use Adminerng\Core\Permissions\PermissionsInterface;
use Nette\Application\UI\Form;

abstract class AbstractDriver implements DriverInterface
{
    protected $connection;

    private $permissions;

    public function name()
    {
        return $this->type() . '.name';
    }

    public final function addFormFields(Form $form)
    {
        return $this->getCredentialsForm()->addFieldsToForm($form);
    }
    
    public function addEditFormFields(Form $form)
    {
        return $this->getCredentialsForm()->addFieldsToForm($form);
    }

    /**
     * @return CredentialsFormInterface
     */
    abstract protected function getCredentialsForm();

    /**
     * @return CredentialsFormInterface
     */
//    abstract protected function getForm();

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
}
