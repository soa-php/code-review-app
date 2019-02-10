<?php

declare(strict_types=1);

namespace Common\Di\Factory;

use Common\Di\Alias\OutgoingMessageStore;
use Interop\Container\ContainerInterface;
use Soa\MessageStore\Publisher\MessageDeliveryService;
use Soa\MessageStore\Publisher\PublishedMessageTracker;
use Zend\ServiceManager\Factory\FactoryInterface;

class MessageDeliveryServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new MessageDeliveryService(
            $container->get(OutgoingMessageStore::class),
            $container->get(PublishedMessageTracker::class)
        );
    }
}
