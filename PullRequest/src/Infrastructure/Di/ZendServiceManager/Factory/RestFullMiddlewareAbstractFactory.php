<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Di\ZendServiceManager\Factory;

use Interop\Container\ContainerInterface;
use PullRequest\Infrastructure\Ui\Http\Restful\Middleware\AbstractRestfulResourceMiddleware;
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
