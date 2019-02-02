<?php

declare(strict_types=1);

namespace UserIdentityTest\UserIdentity\Double;

use Common\Ui\Http\Restful\Authorization\TokenFactory;

class TokenFactoryStub implements TokenFactory
{
    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $refreshToken;

    public static function withTokens(string $accessToken, string $refreshToken): self
    {
        return new self($accessToken, $refreshToken);
    }

    private function __construct(string $accessToken, string $refreshToken)
    {
        $this->accessToken  = $accessToken;
        $this->refreshToken = $refreshToken;
    }

    public function createAccessToken(string $userId, array $userRoles): string
    {
        return $this->accessToken;
    }

    public function createRefreshToken(string $userId, array $userRoles): string
    {
        return $this->refreshToken;
    }
}
