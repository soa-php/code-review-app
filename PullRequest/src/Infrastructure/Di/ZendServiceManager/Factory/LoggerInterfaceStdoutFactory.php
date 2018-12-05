<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Di\ZendServiceManager\Factory;

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
                    echo strtoupper($level) . " || $message" . print_r($this->slice_array_depth($context, 2), true) . "\n";
                }
            }

            private function slice_array_depth($array, $depth = 0)
            {
                foreach ($array as $key => $value) {
                    if (is_array($value)) {
                        if ($depth > 0) {
                            $array[$key] = $this->slice_array_depth($value, $depth - 1);
                        } else {
                            unset($array[$key]);
                        }
                    }
                }

                return $array;
            }
        };
    }
}
