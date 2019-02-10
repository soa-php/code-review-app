<?php

declare(strict_types=1);

namespace Common\Ui\Http\Restful\Authorization\JwtToken;

use Common\Ui\Http\Restful\Authorization\Token;
use Common\Ui\Http\Restful\Authorization\TokenFactory;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer;
use Ramsey\Uuid\Uuid;
use Soa\Clock\Clock;

class JwtTokenFactory implements TokenFactory
{
    public const ISSUER                   = 'issuer';
    public const ACCESS_TOKEN_EXPIRATION  = 'access-expiration';
    public const REFRESH_TOKEN_EXPIRATION = 'refresh-expiration';
    public const KEY                      = 'private_key';

    /**
     * @var array
     */
    private $configuration;

    /**
     * @var Signer
     */
    private $signer;

    /**
     * @var Clock
     */
    private $clock;

    public function __construct(array $configuration, Signer $signer, Clock $clock)
    {
        $this->configuration = $configuration;
        $this->signer        = $signer;
        $this->clock         = $clock;
    }

    public function createAccessToken(string $userId, array $userRoles): string
    {
        $builder = new Builder();
        $builder
            ->setIssuer($this->configuration[self::ISSUER])
            ->setId(Uuid::uuid4()->toString())
            ->setIssuedAt(time())
            ->setExpiration($this->clock->now()->getTimestamp() + $this->configuration[self::ACCESS_TOKEN_EXPIRATION])
            ->set(Token::USER_ID_CLAIM, $userId)
            ->set(Token::USER_ROLES_CLAIM, $userRoles)
            ->set(Token::TOKEN_TYPE_CLAIM, Token::ACCESS_TYPE)
            ->sign($this->signer, $this->configuration[self::KEY]);

        return (string) $builder->getToken();
    }

    public function createRefreshToken(string $userId, array $userRoles): string
    {
        $builder = new Builder();
        $builder
            ->setIssuer($this->configuration[self::ISSUER])
            ->setId(Uuid::uuid4()->toString())
            ->setIssuedAt(time())
            ->setExpiration($this->clock->now()->getTimestamp() + $this->configuration[self::REFRESH_TOKEN_EXPIRATION])
            ->set(Token::USER_ID_CLAIM, $userId)
            ->set(Token::USER_ROLES_CLAIM, $userRoles)
            ->set(Token::TOKEN_TYPE_CLAIM, Token::REFRESH_TYPE)
            ->sign($this->signer, $this->configuration[self::KEY]);

        return (string) $builder->getToken();
    }
}
