<?php

declare(strict_types=1);

namespace Common\Di\Factory;

use Common\Ui\Http\Restful\Middleware\ErrorHandlerMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Zend\ProblemDetails\ProblemDetailsResponseFactory;

class ErrorHandlerMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): ErrorHandlerMiddleware
    {
        return new ErrorHandlerMiddleware($container->get(LoggerInterface::class), $container->get(ProblemDetailsResponseFactory::class));
    }
}
