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

    public function __construct(string $id, string $reviewer)
    {
        $this->reviewer        = $reviewer;
        $this->aggregateRootId = $id;
    }

    public function reviewer(): string
    {
        return $this->reviewer;
    }
}
