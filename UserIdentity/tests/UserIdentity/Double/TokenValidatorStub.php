<?php

declare(strict_types=1);

namespace UserIdentityTest\UserIdentity\Double;

use UserIdentity\Domain\TokenValidator;

class TokenValidatorStub implements TokenValidator
{
    /**
     * @var bool
     */
    private $isTokenValid;

    public static function withValidToken(): self
    {
        return new self(true);
    }

    public static function withInvalidToken(): self
    {
        return new self(false);
    }

    public function __construct(bool $isTokenValid)
    {
        $this->isTokenValid = $isTokenValid;
    }

    public function isValid(string $token): bool
    {
        return $this->isTokenValid;
    }
}
