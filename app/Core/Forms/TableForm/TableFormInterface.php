<?php

namespace UniMan\Core\Forms\TableForm;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

interface TableFormInterface
{
    public function addFieldsToForm(Form $form);

    public function submit(Form $form, ArrayHash $values);
}
