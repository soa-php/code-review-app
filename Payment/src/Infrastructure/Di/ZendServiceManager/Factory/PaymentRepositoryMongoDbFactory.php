<?php

declare(strict_types=1);

namespace Payment\Infrastructure\Di\ZendServiceManager\Factory;

use MongoDB\Database;
use Payment\Infrastructure\Persistence\MongoDb\RepositoryMongoDb;
use Psr\Container\ContainerInterface;
use Soa\EventSourcing\Repository\Repository;

class PaymentRepositoryMongoDbFactory
{
    public function __invoke(ContainerInterface $container): Repository
    {
        /** @var Database $database */
        $database   = $container->get(Database::class);
        $collection = $database->selectCollection('pull_requests');

        return new RepositoryMongoDb($collection, '');
    }
}
