<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory;

use MongoDB\Client;
use MongoDB\Database;
use Psr\Container\ContainerInterface;

class DatabaseMongoFactory
{
    public function __invoke(ContainerInterface $container): Database
    {
        $client = $container->get(Client::class);

        return $client->selectDatabase('merge_pull_request_pm')->withOptions(['typeMap' =>['document' => 'array', 'root' => 'array']]);
    }
}
