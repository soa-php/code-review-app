<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory;

use MergePullRequestPm\Domain\UseCase\MoneyCollectedHandler;
use Psr\Container\ContainerInterface;

class MoneyCollectedHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new MoneyCollectedHandler();
    }
}
