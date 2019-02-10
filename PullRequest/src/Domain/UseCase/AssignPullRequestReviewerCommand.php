<?php

declare(strict_types=1);

namespace PullRequest\Domain\UseCase;

use Soa\EventSourcing\Command\ConventionBasedCommand;

class AssignPullRequestReviewerCommand extends ConventionBasedCommand
{
    /**
     * @var string
     */
    private $reviewer;

    /**
     * @var string
     */
    private $pullRequestId;

    public function __construct(string $pullRequestId, string $reviewer)
    {
        $this->reviewer        = $reviewer;
        $this->pullRequestId   = $pullRequestId;
        $this->aggregateRootId = $pullRequestId;
    }

    public function reviewer(): string
    {
        return $this->reviewer;
    }

    public function pullRequestId(): string
    {
        return $this->pullRequestId;
    }
}
