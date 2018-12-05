<?php

declare(strict_types=1);

namespace PullRequest\Domain\Event;

use Soa\EventSourcing\Event\DomainEvent;

class PullRequestReviewerAssigned implements DomainEvent
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $reviewer;

    public function __construct(string $id, string $reviewer)
    {
        $this->id       = $id;
        $this->reviewer = $reviewer;
    }

    public function streamId(): string
    {
        return $this->id;
    }

    public function reviewer(): string
    {
        return $this->reviewer;
    }
}
