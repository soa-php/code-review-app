<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Di\ZendServiceManager\Factory;

use Psr\Container\ContainerInterface;
use UserIdentity\Domain\TokenFactory;
use UserIdentity\Domain\TokenValidator;
use UserIdentity\Domain\UseCase\RefreshUserAccessTokenCommandHandler;

class RefreshUserAccessTokenCommandHandlerFactory
{
    public function __invoke(ContainerInterface $container): RefreshUserAccessTokenCommandHandler
    {
        return new RefreshUserAccessTokenCommandHandler(
            $container->get(TokenValidator::class),
            $container->get(TokenFactory::class)
        );
    }
}
