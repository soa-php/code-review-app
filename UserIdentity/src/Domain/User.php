<?php

declare(strict_types=1);

namespace UserIdentity\Domain;

use Soa\EventSourcing\Repository\AggregateRoot;

class User implements AggregateRoot
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $refreshToken;

    /**
     * @var array
     */
    private $roles;

    public function __construct(string $id, string $refreshToken = '', array $roles = [])
    {
        $this->id           = $id;
        $this->refreshToken = $refreshToken;
        $this->roles        = $roles;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function refreshToken(): string
    {
        return $this->refreshToken;
    }

    public function roles(): array
    {
        return $this->roles;
    }

    public function withRefreshToken(string $refreshToken): self
    {
        $clone               = clone $this;
        $clone->refreshToken = $refreshToken;

        return $clone;
    }

    public function withRoles(array $roles): self
    {
        $clone        = clone $this;
        $clone->roles = $roles;

        return $clone;
    }
}
