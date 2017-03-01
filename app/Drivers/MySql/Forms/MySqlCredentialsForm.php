<?php

namespace UniMan\Drivers\MySql\Forms;

use Nette\Application\UI\Form;
use UniMan\Core\CredentialsFormInterface;

class MySqlCredentialsForm implements CredentialsFormInterface
{
    public function addFieldsToForm(Form $form)
    {
        $form->addText('server', 'mysql.form.server.label')
            ->setAttribute('placeholder', 'localhost');
        $form->addText('user', 'mysql.form.user.label')
            ->setAttribute('placeholder', 'root')
            ->setAttribute('autofocus');
        $form->addPassword('password', 'mysql.form.password.label');
        $form->addText('database', 'mysql.form.database.label');
    }
}
