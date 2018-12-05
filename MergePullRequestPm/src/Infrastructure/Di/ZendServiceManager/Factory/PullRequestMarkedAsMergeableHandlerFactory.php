<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory;

use MergePullRequestPm\Domain\UseCase\PullRequestMarkedAsMergeableHandler;
use MergePullRequestPm\Domain\Provider\PullRequestProvider;
use MergePullRequestPm\Infrastructure\Persistence\InMemory\PricingProviderInMemory;
use Psr\Container\ContainerInterface;
use Soa\IdentifierGenerator\UuidIdentifierGenerator;

class PullRequestMarkedAsMergeableHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new PullRequestMarkedAsMergeableHandler(new UuidIdentifierGenerator(), $container->get(PullRequestProvider::class), new PricingProviderInMemory());
    }
}
