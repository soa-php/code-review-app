<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Di\ZendServiceManager\Factory\UseCase;

use Common\Ui\Http\Restful\Authorization\TokenFactory;
use Psr\Container\ContainerInterface;
use UserIdentity\Domain\PasswordEncryption;
use UserIdentity\Domain\UseCase\LogUserInWithPasswordCommandHandler;
use UserIdentity\Domain\UserWithPasswordContentValidator;

class RegisterUserWithPasswordCommandHandlerFactory
{
    public function __invoke(ContainerInterface $container): LogUserInWithPasswordCommandHandler
    {
        return new LogUserInWithPasswordCommandHandler(
            $container->get(TokenFactory::class),
            $container->get(PasswordEncryption::class),
            new UserWithPasswordContentValidator()
        );
    }
}
