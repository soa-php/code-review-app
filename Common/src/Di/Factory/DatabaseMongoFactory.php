<?php

declare(strict_types=1);

namespace Common\Di\Factory;

use MongoDB\Client;
use MongoDB\Database;
use Psr\Container\ContainerInterface;

class DatabaseMongoFactory
{
    public function __invoke(ContainerInterface $container): Database
    {
        $client = $container->get(Client::class);

        return $client
            ->selectDatabase($container->get('config')['mongo-db']['database'])
            ->withOptions(['typeMap' =>['document' => 'array', 'root' => 'array']]);
    }
}
