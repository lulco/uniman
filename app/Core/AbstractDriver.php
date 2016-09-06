<?php

namespace Adminerng\Core;

use Nette\Application\UI\Form;

abstract class AbstractDriver implements DriverInterface
{
    protected $connection;

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
}
