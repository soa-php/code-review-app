<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Di\ZendServiceManager\Factory;

use MongoDB\Database;
use Psr\Container\ContainerInterface;
use UserIdentity\Domain\User;
use UserIdentity\Infrastructure\Persistence\MongoDb\RepositoryMongoDb;
use Soa\EventSourcing\Repository\Repository;

class UserRepositoryMongoDbFactory
{
    public function __invoke(ContainerInterface $container): Repository
    {
        /** @var Database $database */
        $database   = $container->get(Database::class);
        $collection = $database->selectCollection('users');

        return new RepositoryMongoDb($collection, User::class);
    }
}
