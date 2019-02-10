<?php

declare(strict_types=1);

namespace Common\Ui\Http\Restful\Authorization;

class AuthorizationRulesDefinitionException extends \RuntimeException
{
    public static function withRoute(string $uri, string $method): self
    {
        return new self("Authorization rules not defined for route: [$method] $uri");
    }
}
