<?php

namespace UniMan\Tests\Drivers\Redis\Forms;

use UniMan\Drivers\Redis\Forms\RedisCredentialsForm;
use UniMan\Tests\Drivers\AbstractDriverTest;
use Nette\Application\UI\Form;
use Nette\Forms\IControl;

class RedisCredentialsFormTest extends AbstractDriverTest
{
    public function testAddFieldsToForm()
    {
        $form = new Form();
        $controls = $form->getControls();
        self::assertCount(0, $controls);
        $credentialsForm = new RedisCredentialsForm();
        $credentialsForm->addFieldsToForm($form);
        self::assertGreaterThan(0, $form->getControls()->count());
        foreach ($form->getControls() as $control) {
            self::assertInstanceOf(IControl::class, $control);
        }
    }
}
