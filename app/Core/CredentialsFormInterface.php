<?php

namespace Adminerng\Core;

use Nette\Application\UI\Form;

interface CredentialsFormInterface
{
    public function addFieldsToForm(Form $form);
}
