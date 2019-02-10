<?php

declare(strict_types=1);

namespace Common\Di\Factory;

use Interop\Container\ContainerInterface;
use Monolog\Handler\StreamHandler;

class MonologFileLoggerHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config    = $container->get('config')['logger-handlers']['file'];
        $formatter = $config['formatter'];
        $logPath = $config['path'];
        $handler = new StreamHandler($logPath, $config['level']);

        $handler->setFormatter(new $formatter());

        return $handler;
    }
}
