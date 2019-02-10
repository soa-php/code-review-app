<?php

declare(strict_types=1);

namespace Common\Di\Factory;

use Psr\Container\ContainerInterface;
use Common\Ui\Http\Restful\Authorization\AuthorizationService;
use Common\Ui\Http\Restful\Authorization\TokenParser;
use Common\Ui\Http\Restful\Middleware\AuthorizationMiddleware;

class AuthorizationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): AuthorizationMiddleware
    {
        return new AuthorizationMiddleware(new AuthorizationService($container->get('config')['authorization-rules']), $container->get(TokenParser::class));
    }
}
