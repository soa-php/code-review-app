<?php

declare(strict_types=1);

namespace PullRequest\Domain\Event;

use Soa\EventSourcing\Event\DomainEvent;

class PullRequestMarkedAsMergeable implements DomainEvent
{
    /**
     * @var string
     */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function streamId(): string
    {
        return $this->id;
    }
}
