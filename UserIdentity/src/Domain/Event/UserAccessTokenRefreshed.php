<?php

declare(strict_types=1);

namespace UserIdentity\Domain\Event;

use Soa\EventSourcing\Event\DomainEvent;

class UserAccessTokenRefreshed implements DomainEvent
{
    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $accessToken;

    public function __construct(string $userId, string $accessToken)
    {
        $this->userId      = $userId;
        $this->accessToken = $accessToken;
    }

    public function streamId(): string
    {
        return $this->userId;
    }

    public function accessToken(): string
    {
        return $this->accessToken;
    }
}
