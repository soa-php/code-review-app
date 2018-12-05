<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Di\ZendServiceManager\Factory;

use MongoDB\Database;
use Psr\Container\ContainerInterface;
use PullRequest\Domain\PullRequest;
use PullRequest\Infrastructure\Persistence\MongoDb\RepositoryMongoDb;
use Soa\EventSourcing\Repository\Repository;

class PullRequestRepositoryMongoDbFactory
{
    public function __invoke(ContainerInterface $container): Repository
    {
        /** @var Database $database */
        $database   = $container->get(Database::class);
        $collection = $database->selectCollection('pull_requests');

        return new RepositoryMongoDb($collection, PullRequest::class);
    }
}
