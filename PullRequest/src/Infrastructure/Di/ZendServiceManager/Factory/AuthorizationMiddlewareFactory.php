<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Di\ZendServiceManager\Factory;

use Psr\Container\ContainerInterface;
use PullRequest\Infrastructure\Ui\Http\Restful\Authorization\AuthorizationService;
use PullRequest\Infrastructure\Ui\Http\Restful\Authorization\TokenParser;
use PullRequest\Infrastructure\Ui\Http\Restful\Middleware\AuthorizationMiddleware;

class AuthorizationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): AuthorizationMiddleware
    {
        return new AuthorizationMiddleware(new AuthorizationService(), $container->get(TokenParser::class));
    }
}
