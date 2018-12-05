<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory;

use Psr\Container\ContainerInterface;
use Soa\MessageStoreAmqp\Publisher\AmqpMessagePublisher;
use Soa\MessageStoreAmqp\Publisher\AmqpPublisherConfig;

class AmqpMessagePublisherFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $managedBoundedContexts = $container->get('config')['managed-bounded-contexts'];
        $processManager         = $container->get('config')['process-manager'];

        return new AmqpMessagePublisher(new AmqpPublisherConfig($processManager, $container->get('config')['rabbitmq']['credentials'], $managedBoundedContexts));
    }
}
