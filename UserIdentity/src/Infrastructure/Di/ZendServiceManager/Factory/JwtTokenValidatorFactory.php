<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Di\ZendServiceManager\Factory;

use Lcobucci\JWT\Signer\Hmac\Sha256;
use Psr\Container\ContainerInterface;
use UserIdentity\Infrastructure\Domain\JwtTokenValidator;

class JwtTokenValidatorFactory
{
    public function __invoke(ContainerInterface $container): JwtTokenValidator
    {
        return new JwtTokenValidator($container->get('config')['jwt'], new Sha256());
    }
}
