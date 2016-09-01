<?php

use Nette\Configurator;

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
return $container;
