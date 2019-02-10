<?php

declare(strict_types=1);

namespace UserIdentity\Domain\Event;

use Soa\EventSourcing\Event\DomainEvent;
use Soa\EventSourcing\Event\FailureDomainEvent;

class RefreshUserAccessTokenFailed implements DomainEvent, FailureDomainEvent
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $reason;

    public static function becauseGivenRefreshTokenIsInvalid(string $userId, string $refreshToken): self
    {
        return new self($userId, "Given refresh token '$refreshToken' is invalid for user '$userId'");
    }

    private function __construct(string $id, string $reason)
    {
        $this->id     = $id;
        $this->reason = $reason;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    public function streamId(): string
    {
        return $this->id;
    }
}
