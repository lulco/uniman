<?php

namespace Adminerng\Core;

use Nette\Application\UI\Form;

abstract class AbstractDriver implements DriverInterface
{
    public final function addFormFields(Form $form)
    {
        return $this->getCredentialsForm()->addFieldsToForm($form);
    }
    
    /**
     * @return CredentialsFormInterface
     */
    abstract protected function getCredentialsForm();
}
