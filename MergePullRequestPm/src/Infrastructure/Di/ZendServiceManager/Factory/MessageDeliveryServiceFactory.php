<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory;

use Interop\Container\ContainerInterface;
use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Alias\OutgoingMessageStore;
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
