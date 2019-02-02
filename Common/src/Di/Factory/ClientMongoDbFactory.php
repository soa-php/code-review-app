<?php

declare(strict_types=1);

namespace Common\Di\Factory;

use MongoDB\Client;
use Psr\Container\ContainerInterface;

class ClientMongoDbFactory
{
    public function __invoke(ContainerInterface $container): Client
    {
        return new Client($container->get('config')['mongo-db']['connection']);
    }
}
