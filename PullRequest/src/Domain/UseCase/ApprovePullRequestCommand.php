<?php

declare(strict_types=1);

namespace PullRequest\Domain\UseCase;

use Soa\EventSourcing\Command\ConventionBasedCommand;

class ApprovePullRequestCommand extends ConventionBasedCommand
{
    /**
     * @var string
     */
    private $approver;

    /**
     * @var string
     */
    private $pullRequestId;

    public function __construct(string $pullRequestId, string $reviewer)
    {
        $this->approver        = $reviewer;
        $this->pullRequestId   = $pullRequestId;
        $this->aggregateRootId = $pullRequestId;
    }

    public function approver(): string
    {
        return $this->approver;
    }

    public function pullRequestId(): string
    {
        return $this->pullRequestId;
    }
}
