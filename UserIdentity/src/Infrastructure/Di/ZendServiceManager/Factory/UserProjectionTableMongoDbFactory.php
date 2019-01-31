<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Di\ZendServiceManager\Factory;

use MongoDB\Database;
use Psr\Container\ContainerInterface;
use UserIdentity\Infrastructure\Persistence\MongoDb\ProjectionTableMongoDb;
use Soa\EventSourcing\Projection\ProjectionTable;

class UserProjectionTableMongoDbFactory
{
    public function __invoke(ContainerInterface $container): ProjectionTable
    {
        /** @var Database $database */
        $database   = $container->get(Database::class);
        $collection = $database->selectCollection('users');

        return new ProjectionTableMongoDb($collection);
    }
}
