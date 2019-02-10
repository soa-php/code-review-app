<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Di\ZendServiceManager\Factory;

use Common\Ui\Http\Restful\Authorization\JwtToken\JwtTokenFactory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Psr\Container\ContainerInterface;
use Soa\Clock\ClockImpl;

class JwtTokenFactoryFactory
{
    public function __invoke(ContainerInterface $container): JwtTokenFactory
    {
        return new JwtTokenFactory($container->get('config')['jwt'], new Sha256(), new ClockImpl());
    }
}
