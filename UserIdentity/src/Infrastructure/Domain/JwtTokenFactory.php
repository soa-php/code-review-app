<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Domain;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer;
use Ramsey\Uuid\Uuid;
use Soa\Clock\Clock;
use UserIdentity\Domain\TokenFactory;

class JwtTokenFactory implements TokenFactory
{
    public const ISSUER                   = 'issuer';
    public const ACCESS_TOKEN_EXPIRATION  = 'access-expiration';
    public const REFRESH_TOKEN_EXPIRATION = 'refresh-expiration';
    public const KEY                      = 'private_key';
    public const CLAIM_USER_ID            = 'user_id';
    public const CLAIM_ROLES              = 'roles';

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
            ->set(self::CLAIM_USER_ID, $userId)
            ->set(self::CLAIM_ROLES, $userRoles)
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
            ->set(self::CLAIM_USER_ID, $userId)
            ->set(self::CLAIM_ROLES, $userRoles)
            ->sign($this->signer, $this->configuration[self::KEY]);

        return (string) $builder->getToken();
    }
}
