<?php

declare(strict_types=1);

namespace MergePullRequestPm\Domain\UseCase;

use MergePullRequestPm\Domain\MergePullRequestProcess;
use MergePullRequestPm\Domain\MergePullRequestState;
use Soa\ProcessManager\Domain\DomainEvent;
use Soa\ProcessManager\Domain\DomainEventHandler;
use Soa\ProcessManager\Domain\Process;
use Soa\ProcessManager\Domain\Transition;

class PullRequestMergedHandler implements DomainEventHandler
{
    /**
     * @param PullRequestMerged       $domainEvent
     * @param MergePullRequestProcess $process
     */
    public function handle(DomainEvent $domainEvent, Process $process): Transition
    {
        $newState = MergePullRequestState::FINISHED();

        return Transition::to($process->withState($newState))->withCommands([]);
    }
}
