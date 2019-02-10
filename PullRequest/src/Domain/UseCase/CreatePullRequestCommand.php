<?php

declare(strict_types=1);

namespace PullRequest\Domain\UseCase;

use Soa\EventSourcing\Command\ConventionBasedCommand;

class CreatePullRequestCommand extends ConventionBasedCommand
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $writer;

    /**
     * @var string
     */
    private $pullRequestId;

    public function __construct(string $pullRequestId, string $code, string $writer)
    {
        $this->code            = $code;
        $this->writer          = $writer;
        $this->pullRequestId   = $pullRequestId;
        $this->aggregateRootId = $pullRequestId;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function writer(): string
    {
        return $this->writer;
    }

    public function pullRequestId(): string
    {
        return $this->pullRequestId;
    }
}
