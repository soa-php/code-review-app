<?php

declare(strict_types=1);

namespace Common\Di\Factory;

use MongoDB\Database;
use Psr\Container\ContainerInterface;
use Soa\Clock\ClockImpl;
use Soa\MessageStoreMongoDb\ErrorMessageTimeoutTrackerMongoDb;

class ErrorMessageTimeoutTrackerMongoDbFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var Database $database */
        $database   = $container->get(Database::class);
        $collection = $database->selectCollection('error_messages');

        return new ErrorMessageTimeoutTrackerMongoDb($collection, new ClockImpl());
    }
}
