<?php

declare(strict_types=1);

namespace MessagePublisher\Infrastructure\Di\ZendServiceManager\Factory;

use Psr\Container\ContainerInterface;
use Soa\MessageStoreAmqp\Publisher\AmqpMessagePublisher;
use Soa\MessageStoreAmqp\Publisher\AmqpPublisherConfig;

class AmqpMessagePublisherFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $recipients = $container->get('config')['message-recipients'];

        return new AmqpMessagePublisher(new AmqpPublisherConfig('message-publisher', $container->get('config')['rabbitmq']['credentials'], $recipients));
    }
}
