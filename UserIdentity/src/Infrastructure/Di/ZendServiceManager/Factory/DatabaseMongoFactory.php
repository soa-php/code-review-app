<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Di\ZendServiceManager\Factory;

use MongoDB\Client;
use MongoDB\Database;
use Psr\Container\ContainerInterface;

class DatabaseMongoFactory
{
    public function __invoke(ContainerInterface $container): Database
    {
        $client = $container->get(Client::class);

        return $client->selectDatabase('user_identity')->withOptions(['typeMap' =>['document' => 'array', 'root' => 'array']]);
    }
}
