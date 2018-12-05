<?php

declare(strict_types=1);

namespace MergePullRequestPm\Domain\UseCase;

use MergePullRequestPm\Domain\Command\CollectMoneyCommand;
use MergePullRequestPm\Domain\Command\PayMoneyCommand;
use MergePullRequestPm\Domain\Command\RecipientAddress;
use MergePullRequestPm\Domain\MergePullRequestProcess;
use MergePullRequestPm\Domain\Provider\PricingProvider;
use MergePullRequestPm\Domain\Provider\PullRequestProvider;
use MergePullRequestPm\Domain\MergePullRequestState;
use Soa\IdentifierGenerator\IdentifierGenerator;
use Soa\ProcessManager\Domain\Command;
use Soa\ProcessManager\Domain\DomainEvent;
use Soa\ProcessManager\Domain\DomainEventHandler;
use Soa\ProcessManager\Domain\InvalidStateTransitionException;
use Soa\ProcessManager\Domain\Process;
use Soa\ProcessManager\Domain\Transition;

class PullRequestMarkedAsMergeableHandler implements DomainEventHandler
{
    /**
     * @var PullRequestProvider
     */
    private $pullRequestProvider;

    /**
     * @var PricingProvider
     */
    private $pricingProvider;

    /**
     * @var IdentifierGenerator
     */
    private $identifierGenerator;

    public function __construct(IdentifierGenerator $identifierGenerator, PullRequestProvider $pullRequestProvider, PricingProvider $pricingProvider)
    {
        $this->pullRequestProvider = $pullRequestProvider;
        $this->pricingProvider     = $pricingProvider;
        $this->identifierGenerator = $identifierGenerator;
    }

    /**
     * @param PullRequestMarkedAsMergeable $domainEvent
     * @param MergePullRequestProcess      $process
     */
    public function handle(DomainEvent $domainEvent, Process $process): Transition
    {
        $newState = MergePullRequestState::EXCHANGING_MONEY();
        if (MergePullRequestState::INITIALIZED() !== $process->currentState()) {
            InvalidStateTransitionException::ofProcess($process)->fromState($process->currentState())->toState($newState)->throw();
        }

        $commands        = $this->buildCommands($domainEvent, $process);
        $pendingPayments = [];
        foreach ($commands as $command) {
            $pendingPayments[] = $command->streamId();
        }

        $process = $process
            ->withPendingPayments($pendingPayments)
            ->withPullRequestId($domainEvent->streamId())
            ->withState($newState);

        return Transition::to($process)->withCommands($commands);
    }

    /**
     * @return Command[]
     */
    private function buildCommands(PullRequestMarkedAsMergeable $event, MergePullRequestProcess $processManager): array
    {
        return array_merge([$this->buildCollectMoneyCommand($event, $processManager)], $this->buildPayMoneyCommands($event, $processManager));
    }

    private function buildCollectMoneyCommand(PullRequestMarkedAsMergeable $event, MergePullRequestProcess $process): CollectMoneyCommand
    {
        $transactionId = $this->identifierGenerator->nextIdentity();
        $pullRequest   = $this->pullRequestProvider->ofId($event->streamId());
        $collectMoney  = $this->pricingProvider->mergeFee();

        return (new CollectMoneyCommand($transactionId, $pullRequest->writer(), $collectMoney->getAmount(), $collectMoney->getCurrency()->getCode(), $pullRequest->pullRequestId()))
            ->withProcessId($process->id())
            ->withStreamId($transactionId)
            ->withRecipient(RecipientAddress::PAYMENTS);
    }

    private function buildPayMoneyCommands(PullRequestMarkedAsMergeable $event, MergePullRequestProcess $process): array
    {
        $pullRequest = $this->pullRequestProvider->ofId($event->streamId());
        $payment     = $this->pricingProvider->mergeReward();
        $commands    = [];

        foreach ($pullRequest->reviewers() as $reviewer) {
            $transactionId = $this->identifierGenerator->nextIdentity();
            $commands[]    = (new PayMoneyCommand($transactionId, $reviewer, $payment->getAmount(), $payment->getCurrency()->getCode(), $pullRequest->pullRequestId()))
                            ->withProcessId($process->id())
                            ->withRecipient(RecipientAddress::PAYMENTS)
                            ->withStreamId($transactionId);
        }

        return $commands;
    }
}
