<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory;

use MergePullRequestPm\Infrastructure\Persistence\MongoDb\PullRequestProviderMongoDb;
use MongoDB\Client;
use Psr\Container\ContainerInterface;

class PullRequestProviderMongoDbFactory
{
    public function __invoke(ContainerInterface $container): PullRequestProviderMongoDb
    {
        /** @var Client $client */
        $client   = $container->get(Client::class);
        $database = $client->selectDatabase('code_review')->withOptions(['typeMap' =>['document' => 'array', 'root' => 'array']]);

        return new PullRequestProviderMongoDb($database->selectCollection('pull_requests'));
    }
}
