<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Di\ZendServiceManager\Factory;

use MongoDB\Database;
use Psr\Container\ContainerInterface;
use Soa\MessageStoreMongoDb\PublishedMessageTrackerMongoDb;

class PublishedMessageTrackerMongoDbFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var Database $database */
        $database              = $container->get(Database::class);
        $trackerCollection     = $database->selectCollection('messages_tracker');

        return new PublishedMessageTrackerMongoDb($trackerCollection);
    }
}
