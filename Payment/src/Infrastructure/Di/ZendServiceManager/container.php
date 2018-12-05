<?php

declare(strict_types=1);

use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

$config    = require __DIR__ . '/config.php';
$container = new ServiceManager();
(new Config($config['dependencies']))->configureServiceManager($container);
$container->setService('config', $config);

return $container;
