<?php

namespace UniMan\Core;

use Nette\Application\UI\Form;

interface CredentialsFormInterface
{
    public function addFieldsToForm(Form $form);
}
