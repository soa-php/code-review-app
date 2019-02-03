<?php

declare(strict_types=1);

namespace Common\Di\Factory;

use Psr\Container\ContainerInterface;
use Monolog\Logger;

class MonologLoggerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $enabledLoggerHandlers = array_values($container->get('config')['enabled-loggers']);
        $serviceName = $container->get('config')['service-name'];

        $logger = new Logger($serviceName);

        foreach ($enabledLoggerHandlers as $loggerHandler) {
            $loggerHandlerFactory = new $loggerHandler();
            $logger->pushHandler($loggerHandlerFactory($container));
        }

        return $logger;
    }
}
