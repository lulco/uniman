<?php

use Nette\Application\UI\Form;
use Nette\Configurator;
use RadekDostal\NetteComponents\DateTimePicker\TbDatePicker;
use RadekDostal\NetteComponents\DateTimePicker\TbDateTimePicker;

require __DIR__ . '/../vendor/autoload.php';

umask(0);

$configurator = new Configurator();

$configurator->setDebugMode(false);

$tempDir = sys_get_temp_dir() . '/adminerng/temp';
if (!file_exists($tempDir)) {
    mkdir($tempDir, 0777, true);
}
$configurator->setTempDirectory($tempDir);

$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$container = $configurator->createContainer();

Form::extensionMethod('addDatePicker', function (Form $_this, $name, $label, $cols = null, $maxLength = null) {
    $datePicker = new TbDatePicker($label, $cols, $maxLength);
    $datePicker->setFormat('Y-m-d');
    return $_this[$name] = $datePicker;
});

Form::extensionMethod('addDateTimePicker', function (Form $_this, $name, $label, $cols = null, $maxLength = null) {
    $dateTimePicker = new TbDateTimePicker($label, $cols, $maxLength);
    $dateTimePicker->setFormat('Y-m-d H:i:s');
    return $_this[$name] = $dateTimePicker;
});

return $container;
