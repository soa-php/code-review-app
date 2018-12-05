<?php

declare(strict_types=1);

namespace PullRequest\Domain\Event;

use Soa\EventSourcing\Event\DomainEvent;

class PullRequestApproved implements DomainEvent
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $approver;

    public function __construct(string $id, string $approver)
    {
        $this->id       = $id;
        $this->approver = $approver;
    }

    public function approver(): string
    {
        return $this->approver;
    }

    public function streamId(): string
    {
        return $this->id;
    }
}
