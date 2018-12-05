<?php

declare(strict_types=1);

namespace Payment\Infrastructure\Di\ZendServiceManager\Factory;

use Interop\Container\ContainerInterface;
use Payment\Infrastructure\Ui\Http\Restful\Resource\AbstractRestfulResourceMiddleware;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Class RestFullMiddlewareAbstractFactory.
 */
class RestFullMiddlewareAbstractFactory implements AbstractFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return is_subclass_of($requestedName, AbstractRestfulResourceMiddleware::class);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new $requestedName($container);
    }
}
