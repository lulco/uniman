<?php

use Nette\Application\UI\Form;
use Nette\Configurator;
use RadekDostal\NetteComponents\DateTimePicker\TbDatePicker;
use RadekDostal\NetteComponents\DateTimePicker\TbDateTimePicker;

if (filter_input(INPUT_GET, 'debug') == 1) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

require __DIR__ . '/../vendor/autoload.php';

umask(0);

$configurator = new Configurator();

$configurator->setDebugMode(false);

$tempDir = sys_get_temp_dir() . '/uniman/temp';
if (!file_exists($tempDir)) {
    mkdir($tempDir, 0777, true);
}
$configurator->setTempDirectory($tempDir);

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
