<?php

declare(strict_types=1);

namespace Common\Di\Factory;

use Interop\Container\ContainerInterface;
use Common\Ui\Messaging\Listener\AbstractMessageListener;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class AbstractMessageListenerFactory implements AbstractFactoryInterface
{
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return is_subclass_of($requestedName, AbstractMessageListener::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new $requestedName($container);
    }
}
