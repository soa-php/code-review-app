<?php

declare(strict_types=1);

namespace MergePullRequestPm\Domain\UseCase;

use MergePullRequestPm\Domain\Command\MergePullRequestCommand;
use MergePullRequestPm\Domain\Command\RecipientAddress;
use MergePullRequestPm\Domain\MergePullRequestProcess;
use MergePullRequestPm\Domain\MergePullRequestState;
use Soa\ProcessManager\Domain\CommandBuilder;
use Soa\ProcessManager\Domain\DomainEvent;
use Soa\ProcessManager\Domain\DomainEventHandler;
use Soa\ProcessManager\Domain\InvalidStateTransitionException;
use Soa\ProcessManager\Domain\Process;
use Soa\ProcessManager\Domain\Transition;

class MoneyPayedHandler implements DomainEventHandler
{
    /**
     * @param MoneyPayed              $domainEvent
     * @param MergePullRequestProcess $process
     */
    public function handle(DomainEvent $domainEvent, Process $process): Transition
    {
        $process        = $this->accountMoneyWasPayed($domainEvent, $process);
        $newState       = MergePullRequestState::MERGING();
        $nextCommands[] = CommandBuilder::buildCommand(new MergePullRequestCommand($process->pullRequestId()))
            ->withRecipient(RecipientAddress::PULL_REQUEST)
            ->withProcessId($process->id())
            ->withStreamId($process->pullRequestId())
            ->create();

        if ($process->pendingPayments()) {
            $newState     = $process->currentState();
            $nextCommands = [];
        }

        if ($process->currentState()->isNot(MergePullRequestState::EXCHANGING_MONEY())) {
            InvalidStateTransitionException::ofProcess($process)->fromState($process->currentState())->toState($newState)->throw();
        }

        return Transition::to($process->withState($newState))->withCommands($nextCommands);
    }

    private function accountMoneyWasPayed(DomainEvent $domainEvent, MergePullRequestProcess $process): MergePullRequestProcess
    {
        return $process->withPendingPayments(array_values(array_diff($process->pendingPayments(), [$domainEvent->streamId()])));
    }
}
