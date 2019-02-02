<?php

declare(strict_types=1);

namespace Common\Ui\Http\Restful\Authorization;

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

    public function isAuthDefinedForRoute(string $uri, string $method): bool
    {
        if (!isset($this->authorizationRules[$uri])) {
            return false;
        }

        if (!isset($this->authorizationRules[$uri][$method])) {
            return false;
        }

        return true;
    }

    /**
     * @throws AuthorizationRulesDefinitionException
     */
    public function isAuthRequiredForRoute(string $uri, string $method): bool
    {
        if (!$this->isAuthDefinedForRoute($uri, $method)) {
            throw AuthorizationRulesDefinitionException::withRoute($uri, $method);
        }

        if (AuthorizationType::NO_AUTH === $this->authorizationRules[$uri][$method]) {
            return false;
        }

        return true;
    }

    public function isUserAuthorizedForRoute(array $userRoles, string $uri, string $method): bool
    {
        if (!$this->isAuthRequiredForRoute($uri, $method)) {
            return true;
        }

        return array_reduce(
            $userRoles,
            function (bool $isAlreadyAuthorized, string $userRole) use ($uri, $method) {
                if ($isAlreadyAuthorized) {
                    return true;
                }

                return in_array($userRole, $this->authorizationRules[$uri][$method]);
            },
            false
        );
    }
}
