<?php

declare(strict_types=1);

namespace Payment\Infrastructure\Di\ZendServiceManager\Factory;

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
            new AmqpSubscriberConfig($container->get('config')['rabbitmq']['credentials'], $container->get('config')['bounded-context']),
            new AmqpErrorMessageHandler(
                $container->get(ErrorMessageTimeoutTracker::class),
                new ClockImpl(),
                5
            )
        );
    }
}
