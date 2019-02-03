<?php

declare(strict_types=1);

namespace PullRequest\Domain\UseCase;

use Soa\EventSourcing\Command\ConventionBasedCommand;

class MergePullRequestCommand extends ConventionBasedCommand
{
    /**
     * @var string
     */
    private $pullRequestId;

    public function __construct(string $pullRequestId)
    {
        $this->pullRequestId   = $pullRequestId;
        $this->aggregateRootId = $pullRequestId;
    }

    public function pullRequestId(): string
    {
        return $this->pullRequestId;
    }
}
