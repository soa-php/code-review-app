<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory;

use MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Alias\DatabaseIdentifierGenerator;
use MongoDB\Database;
use Psr\Container\ContainerInterface;
use Soa\MessageStoreMongoDb\MessageStoreMongoDb;

class OutgoingMessageStoreMongoDbFactory
{
    public function __invoke(ContainerInterface $container): MessageStoreMongoDb
    {
        /** @var Database $database */
        $database              = $container->get(Database::class);
        $messagesCollection    = $database->selectCollection('outgoing_messages');
        $identifierGenerator   = $container->get(DatabaseIdentifierGenerator::class);

        return new MessageStoreMongoDb($messagesCollection, $identifierGenerator);
    }
}
