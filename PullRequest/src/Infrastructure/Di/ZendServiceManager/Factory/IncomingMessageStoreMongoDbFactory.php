<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Di\ZendServiceManager\Factory;

use MongoDB\Database;
use Psr\Container\ContainerInterface;
use PullRequest\Infrastructure\Di\ZendServiceManager\Alias\DatabaseIdentifierGenerator;
use Soa\MessageStore\MessageStore;
use Soa\MessageStoreMongoDb\MessageStoreMongoDb;

class IncomingMessageStoreMongoDbFactory
{
    public function __invoke(ContainerInterface $container): MessageStore
    {
        /** @var Database $database */
        $database              = $container->get(Database::class);
        $messagesCollection    = $database->selectCollection('incoming_messages');
        $identifierGenerator   = $container->get(DatabaseIdentifierGenerator::class);

        return new MessageStoreMongoDb($messagesCollection, $identifierGenerator);
    }
}
