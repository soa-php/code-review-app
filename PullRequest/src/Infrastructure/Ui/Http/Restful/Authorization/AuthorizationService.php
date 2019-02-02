<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Ui\Http\Restful\Authorization;

class AuthorizationService
{
    /**
     * @var array
     */
    private $authorizationRules;

    public function __construct(array $authorizationRules)
    {
        $this->authorizationRules = $authorizationRules;
    }

    public function isAuthRequiredForRoute(string $route, string $method): bool
    {
        if (!isset($this->authorizationRules[$route])) {
            return false;
        }

        if (!isset($this->authorizationRules[$route][$method])) {
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

                return in_array($userRole, $this->authorizationRules[$route][$method]);
            },
            false
        );
    }
}
