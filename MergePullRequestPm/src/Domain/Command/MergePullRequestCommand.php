<?php

declare(strict_types=1);

namespace MergePullRequestPm\Domain\Command;

use Soa\ProcessManager\Domain\Command;

class MergePullRequestCommand extends Command
{
    /**
     * @var string
     */
    private $aggregateRootId;

    public function __construct(string $aggregateRootId)
    {
        $this->aggregateRootId   = $aggregateRootId;
    }

    public function aggregateRootId(): string
    {
        return $this->aggregateRootId;
    }
}
