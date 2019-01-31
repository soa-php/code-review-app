<?php

declare(strict_types=1);

namespace UserIdentity\Domain\Event;

use Soa\EventSourcing\Event\DomainEvent;
use Soa\EventSourcing\Event\FailureDomainEvent;

class LogUserInWithPasswordFailed implements DomainEvent, FailureDomainEvent
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $reason;

    public static function withReason(string $id, string $reason): self
    {
        return new self($id, $reason);
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
