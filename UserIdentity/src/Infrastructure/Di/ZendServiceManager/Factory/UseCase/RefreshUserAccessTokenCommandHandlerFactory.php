<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\UseCase;

use Common\Ui\Http\Restful\Authorization\TokenFactory;
use Common\Ui\Http\Restful\Authorization\TokenValidator;
use Psr\Container\ContainerInterface;
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
