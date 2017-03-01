<?php

namespace UniMan\Core\Forms\ItemForm;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

interface ItemFormInterface
{
    public function addFieldsToForm(Form $form);

    public function submit(Form $form, ArrayHash $values);
}
