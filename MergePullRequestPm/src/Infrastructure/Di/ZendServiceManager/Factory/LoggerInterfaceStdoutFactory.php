<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory;

use Psr\Container\ContainerInterface;
use Psr\Log\AbstractLogger;

class LoggerInterfaceStdoutFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new class() extends AbstractLogger {
            /**
             * Logs with an arbitrary level.
             *
             * @param mixed  $level
             * @param string $message
             * @param array  $context
             */
            public function log($level, $message, array $context = [])
            {
                if (!$context) {
                    echo strtoupper($level) . " || $message" . "\n";
                } else {
                    echo strtoupper($level) . " || $message" . print_r($context, true) . "\n";
                }
            }
        };
    }
}
