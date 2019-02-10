<?php

declare(strict_types=1);

namespace Common\Di\Factory;

use Psr\Container\ContainerInterface;
use Soa\Clock\ClockImpl;
use Soa\MessageStore\Subscriber\Error\ErrorMessageTimeoutTracker;
use Soa\MessageStore\Subscriber\Listener\MessageRouter;
use Soa\MessageStoreAmqp\Subscriber\AmqpMessageSubscriber;
use Soa\MessageStoreAmqp\Subscriber\AmqpSubscriberConfig;
use Soa\MessageStoreAmqp\Subscriber\Error\AmqpErrorMessageHandler;

class AmqpMessageSubscriberFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new AmqpMessageSubscriber(
            $container->get(MessageRouter::class),
            new AmqpSubscriberConfig($container->get('config')['rabbitmq']['credentials'], $container->get('config')['service-name']),
            new AmqpErrorMessageHandler(
                $container->get(ErrorMessageTimeoutTracker::class),
                new ClockImpl(),
                $container->get('config')['rabbitmq']['dead-letter-seconds']
            )
        );
    }
}
