<?php

use Nette\Configurator;

require __DIR__ . '/../vendor/autoload.php';

umask(0);

$configurator = new Configurator();

$host = filter_input(INPUT_SERVER, 'HTTP_HOST');
$debug = (bool)preg_match('/localhost|devel/', $host);

$configurator->setDebugMode($debug);
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$container = $configurator->createContainer();
return $container;
