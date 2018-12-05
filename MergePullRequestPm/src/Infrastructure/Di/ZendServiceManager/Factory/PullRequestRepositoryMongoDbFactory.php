<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory;

use MongoDB\Database;
use Psr\Container\ContainerInterface;
use Soa\ProcessManager\Infrastructure\Persistence\Repository;

class MergePullRequestPmRepositoryMongoDbFactory
{
    public function __invoke(ContainerInterface $container): Repository
    {
        /** @var Database $database */
        $database   = $container->get(Database::class);
        $collection = $database->selectCollection('pull_requests');

        return new RepositoryMongoDb($collection, MergePullRequestPm::class);
    }
}
