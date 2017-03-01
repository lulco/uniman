<?php

namespace UniMan\Core\Forms\DatabaseForm;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

interface DatabaseFormInterface
{
    public function addFieldsToForm(Form $form);

    public function submit(Form $form, ArrayHash $values);
}
