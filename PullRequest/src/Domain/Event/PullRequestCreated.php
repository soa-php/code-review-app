<?php

declare(strict_types=1);

namespace PullRequest\Domain\Event;

use Soa\EventSourcing\Event\DomainEvent;

class PullRequestCreated implements DomainEvent
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $writer;

    /**
     * @var string
     */
    private $code;

    public function __construct(string $id, string $writer, string $code)
    {
        $this->id     = $id;
        $this->writer = $writer;
        $this->code   = $code;
    }

    public function streamId(): string
    {
        return $this->id;
    }

    public function writer(): string
    {
        return $this->writer;
    }

    public function code(): string
    {
        return $this->code;
    }
}
