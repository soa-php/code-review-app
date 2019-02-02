<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Ui\Http\Restful\Authorization;

class AuthorizationService
{
    public function isAuthRequiredForRoute(string $route, string $method): bool
    {
        if (!isset(AuthorizationRules::getRules()[$route])) {
            return false;
        }

        if (!isset(AuthorizationRules::getRules()[$route][$method])) {
            return false;
        }

        return true;
    }

    public function isUserAuthorizedForRoute(array $userRoles, string $route, string $method): bool
    {
        if (!$this->isAuthRequiredForRoute($route, $method)) {
            return true;
        }

        return array_reduce(
            $userRoles,
            function (bool $isAlreadyAuthorized, string $userRole) use ($route, $method) {
                if ($isAlreadyAuthorized) {
                    return true;
                }

                return in_array($userRole, AuthorizationRules::getRules()[$route][$method]);
            },
            false
        );
    }
}
