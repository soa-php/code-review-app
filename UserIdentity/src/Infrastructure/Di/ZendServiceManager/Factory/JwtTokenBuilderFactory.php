<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Di\ZendServiceManager\Factory;

use Lcobucci\JWT\Signer\Hmac\Sha256;
use Psr\Container\ContainerInterface;
use Soa\Clock\ClockImpl;
use UserIdentity\Infrastructure\Domain\JwtTokenFactory;

class JwtTokenBuilderFactory
{
    public function __invoke(ContainerInterface $container): JwtTokenFactory
    {
        return new JwtTokenFactory($container->get('config')['jwt'], new Sha256(), new ClockImpl());
    }
}
