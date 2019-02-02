<?php

declare(strict_types=1);

namespace Common\Di\Factory;

use Psr\Container\ContainerInterface;
use Soa\MessageStoreAmqp\Publisher\AmqpMessagePublisher;
use Soa\MessageStoreAmqp\Publisher\AmqpPublisherConfig;

class AmqpMessagePublisherFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $serviceName = $container->get('config')['service-name'];

        return new AmqpMessagePublisher(new AmqpPublisherConfig($serviceName, $container->get('config')['rabbitmq']['credentials'], [$serviceName]));
    }
}
