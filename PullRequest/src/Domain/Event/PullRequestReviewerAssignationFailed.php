<?php

declare(strict_types=1);

namespace PullRequest\Domain\Event;

use Soa\EventSourcing\Event\DomainEvent;
use Soa\EventSourcing\Event\FailureDomainEvent;

class PullRequestReviewerAssignationFailed implements DomainEvent, FailureDomainEvent
{
    public const EMPTY_REVIEWER            = 'empty reviewer';
    public const MAX_REVIEWERS_ASSIGNED    = 'max reviewers assigned';
    public const REVIEWER_ALREADY_ASSIGNED = 'reviewer already assigned';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $reviewer;

    /**
     * @var string
     */
    private $reason;

    public function __construct(string $id, string $reviewer, string $reason)
    {
        $this->id         = $id;
        $this->reviewer   = $reviewer;
        $this->reason     = $reason;
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
