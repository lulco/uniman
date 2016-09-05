<?php

namespace Adminerng\Drivers\MySql;

use Adminerng\Core\CredentialsFormInterface;
use Nette\Application\UI\Form;

class MySqlForm implements CredentialsFormInterface
{
    public function addFieldsToForm(Form $form)
    {
        $form->addText('server', 'mysql.form.server.label')
            ->setAttribute('placeholder', 'localhost');
        $form->addText('user', 'mysql.form.user.label');
        $form->addPassword('password', 'mysql.form.password.label');
        $form->addText('database', 'mysql.form.database.label');
    }
}
