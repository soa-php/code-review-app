<?php

declare(strict_types=1);

namespace Payment\Infrastructure\Di\ZendServiceManager\Factory;

use MongoDB\Database;
use Payment\Infrastructure\Di\ZendServiceManager\Alias\DatabaseIdentifierGenerator;
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
