<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Di\ZendServiceManager\Factory;

use Psr\Container\ContainerInterface;
use Soa\MessageStoreAmqp\Publisher\AmqpMessagePublisher;
use Soa\MessageStoreAmqp\Publisher\AmqpPublisherConfig;

class AmqpMessagePublisherFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $boundedContext = $container->get('config')['bounded-context'];

        return new AmqpMessagePublisher(new AmqpPublisherConfig($boundedContext, $container->get('config')['rabbitmq']['credentials'], [$boundedContext]));
    }
}
