<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Di\ZendServiceManager\Factory;

use Psr\Container\ContainerInterface;
use Soa\MessageStore\Subscriber\Listener\MessageRouter;

class MessageRouterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new MessageRouter($container);
    }
}
