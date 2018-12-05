<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory;

use MergePullRequestPm\Domain\MergePullRequestProcess;
use MergePullRequestPm\Domain\MergePullRequestState;
use MergePullRequestPm\Infrastructure\Persistence\MongoDb\RepositoryMongoDb;
use MongoDB\Database;
use Psr\Container\ContainerInterface;
use Soa\IdentifierGenerator\UuidIdentifierGenerator;

class RepositoryMongoDbFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var Database $database */
        $database                    = $container->get(Database::class);
        $processManagerCollection    = $database->selectCollection('process_managers');

        return new RepositoryMongoDb($processManagerCollection, MergePullRequestProcess::class, MergePullRequestState::class, new UuidIdentifierGenerator());
    }
}
