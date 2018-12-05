<?php

declare(strict_types=1);

namespace PullRequest\Domain\Event;

use Soa\EventSourcing\Event\DomainEvent;
use Soa\EventSourcing\Event\FailureDomainEvent;

class PullRequestCreationFailed implements DomainEvent, FailureDomainEvent
{
    public const EMPTY_CODE   = 'empty code';
    public const EMPTY_WRITER = 'empty writer';

    /**
     * @var string
     */
    private $writer;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $reason;

    public function __construct(string $writer, string $code, string $reason)
    {
        $this->writer = $writer;
        $this->code   = $code;
        $this->reason = $reason;
    }

    public function streamId(): string
    {
        return 'n/a';
    }

    public function reason(): string
    {
        return $this->reason;
    }
}
