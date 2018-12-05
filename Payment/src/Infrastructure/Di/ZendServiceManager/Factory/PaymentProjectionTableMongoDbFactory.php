<?php

declare(strict_types=1);

namespace Payment\Infrastructure\Di\ZendServiceManager\Factory;

use MongoDB\Database;
use Payment\Infrastructure\Persistence\MongoDb\ProjectionTableMongoDb;
use Psr\Container\ContainerInterface;
use Soa\EventSourcing\Projection\ProjectionTable;

class PaymentProjectionTableMongoDbFactory
{
    public function __invoke(ContainerInterface $container): ProjectionTable
    {
        /** @var Database $database */
        $database   = $container->get(Database::class);
        $collection = $database->selectCollection('payments');

        return new ProjectionTableMongoDb($collection);
    }
}
