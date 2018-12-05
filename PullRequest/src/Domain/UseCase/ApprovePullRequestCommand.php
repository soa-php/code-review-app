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

    public function __construct(string $id, string $reviewer)
    {
        $this->approver        = $reviewer;
        $this->aggregateRootId = $id;
    }

    public function approver(): string
    {
        return $this->approver;
    }
}
