<?php

declare(strict_types=1);

namespace Common\Di\Factory;

use Common\Ui\Http\Restful\Middleware\AbstractRestfulResourceMiddleware;
use Interop\Container\ContainerInterface;
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
