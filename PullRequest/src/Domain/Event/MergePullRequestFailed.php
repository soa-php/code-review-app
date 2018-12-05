<?php

declare(strict_types=1);

namespace PullRequest\Domain\Event;

use Soa\EventSourcing\Event\DomainEvent;
use Soa\EventSourcing\Event\FailureDomainEvent;

class MergePullRequestFailed implements DomainEvent, FailureDomainEvent
{
    public const NOT_MERGEABLE  = 'is not mergeable';
    public const ALREADY_MERGED = 'already merged';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $reason;

    public function __construct(string $id, string $reason)
    {
        $this->id     = $id;
        $this->reason = $reason;
    }

    public function streamId(): string
    {
        return $this->id;
    }

    public function reason(): string
    {
        return $this->reason;
    }
}
