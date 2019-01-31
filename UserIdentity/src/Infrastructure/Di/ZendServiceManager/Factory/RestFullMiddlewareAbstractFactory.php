<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Di\ZendServiceManager\Factory;

use Interop\Container\ContainerInterface;
use UserIdentity\Infrastructure\Ui\Http\Restful\Resource\AbstractRestfulResourceMiddleware;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class RestFullMiddlewareAbstractFactory implements AbstractFactoryInterface
{
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return is_subclass_of($requestedName, AbstractRestfulResourceMiddleware::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new $requestedName($container);
    }
}
