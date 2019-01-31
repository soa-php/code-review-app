<?php

declare(strict_types=1);

namespace UserIdentity\Domain\Event;

use Soa\EventSourcing\Event\DomainEvent;

class UserWithPasswordLoggedIn implements DomainEvent
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $email;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $refreshToken;

    public function __construct(
        string $id,
        string $username,
        string $password,
        string $email,
        array $roles,
        string $accessToken,
        string $refreshToken
    ) {
        $this->id                 = $id;
        $this->username           = $username;
        $this->password           = $password;
        $this->email              = $email;
        $this->roles              = $roles;
        $this->accessToken        = $accessToken;
        $this->refreshToken       = $refreshToken;
    }

    public function streamId(): string
    {
        return $this->id;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function roles(): array
    {
        return $this->roles;
    }

    public function accessToken(): string
    {
        return $this->accessToken;
    }

    public function refreshToken(): string
    {
        return $this->refreshToken;
    }
}
