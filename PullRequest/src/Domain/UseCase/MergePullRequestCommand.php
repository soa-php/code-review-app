<?php

declare(strict_types=1);

namespace PullRequest\Domain\UseCase;

use Soa\EventSourcing\Command\ConventionBasedCommand;

class MergePullRequestCommand extends ConventionBasedCommand
{
    public function __construct(string $id)
    {
        $this->aggregateRootId = $id;
    }
}
