<?php

namespace UniMan\Core\Forms\FilterForm;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

interface FilterFormInterface
{
    public function filter(Form $form, ArrayHash $values);
}
