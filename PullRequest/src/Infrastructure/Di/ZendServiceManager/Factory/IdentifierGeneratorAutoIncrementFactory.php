<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Di\ZendServiceManager\Factory;

use MongoDB\Database;
use Psr\Container\ContainerInterface;
use Soa\IdentifierGenerator\IdentifierGenerator;
use Soa\IdentifierGeneratorMongoDb\IdentifierGeneratorAutoIncrementMongoDb;

class IdentifierGeneratorAutoIncrementFactory
{
    public function __invoke(ContainerInterface $container): IdentifierGenerator
    {
        /** @var Database $database */
        $database              = $container->get(Database::class);
        $identifiersCollection = $database->selectCollection('identifiers');

        return new IdentifierGeneratorAutoIncrementMongoDb($identifiersCollection);
    }
}
