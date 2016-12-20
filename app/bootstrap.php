<?php

use Nette\Application\UI\Form;
use Nette\Configurator;
use RadekDostal\NetteComponents\DateTimePicker\TbDatePicker;
use RadekDostal\NetteComponents\DateTimePicker\TbDateTimePicker;

require __DIR__ . '/../vendor/autoload.php';

umask(0);

$configurator = new Configurator();

$host = filter_input(INPUT_SERVER, 'HTTP_HOST');
$debug = (bool)preg_match('/localhost|devel/', $host);

$configurator->setDebugMode($debug);
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->addConfig(__DIR__ . '/config/config.neon');
$container = $configurator->createContainer();

Form::extensionMethod('addDatePicker', function (Form $form, $name, $label, $maxLength = null) {
    $datePicker = new TbDatePicker($label, $maxLength);
    $datePicker->setFormat('Y-m-d');
    $datePicker->setAttribute('class', 'datepicker');
    return $form[$name] = $datePicker;
});

Form::extensionMethod('addDateTimePicker', function (Form $form, $name, $label, $maxLength = null) {
    $dateTimePicker = new TbDateTimePicker($label, $maxLength);
    $dateTimePicker->setFormat('Y-m-d H:i:s');
    $dateTimePicker->setAttribute('class', 'datetimepicker');
    return $form[$name] = $dateTimePicker;
});

return $container;
