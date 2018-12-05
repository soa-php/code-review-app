<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Di\ZendServiceManager\Factory;

use MongoDB\Database;
use Psr\Container\ContainerInterface;
use PullRequest\Infrastructure\Persistence\MongoDb\ProjectionTableMongoDb;
use Soa\EventSourcing\Projection\ProjectionTable;

class PullRequestProjectionTableMongoDbFactory
{
    public function __invoke(ContainerInterface $container): ProjectionTable
    {
        /** @var Database $database */
        $database   = $container->get(Database::class);
        $collection = $database->selectCollection('pull_requests');

        return new ProjectionTableMongoDb($collection);
    }
}
