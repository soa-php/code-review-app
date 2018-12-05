<?php

declare(strict_types=1);

namespace PullRequest\Domain\Event;

use Soa\EventSourcing\Event\DomainEvent;
use Soa\EventSourcing\Event\FailureDomainEvent;

class ApprovePullRequestFailed implements DomainEvent, FailureDomainEvent
{
    public const ALREADY_MARKED_AS_MERGEABLE = 'Pull Request was already marked as mergeable';
    public const APPROVER_IS_NOT_REVIEWER    = 'Approver is not assigned as reviewer';
    public const APPROVER_ALREADY_APPROVED   = 'Approver already approved this Pull Request';

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
        $this->id       = $id;
        $this->reviewer = $reviewer;
        $this->reason   = $reason;
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
