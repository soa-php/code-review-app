<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Di\ZendServiceManager\Factory;

use MergePullRequestPm\Domain\UseCase\MoneyPayedHandler;
use Psr\Container\ContainerInterface;

class MoneyPayedHandlerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new MoneyPayedHandler();
    }
}
