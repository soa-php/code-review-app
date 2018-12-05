<?php

declare(strict_types=1);

namespace MergePullRequestPmTest\UseCase;

use MergePullRequestPm\Domain\Command\MergePullRequestCommand;
use MergePullRequestPm\Domain\Command\RecipientAddress;
use MergePullRequestPm\Domain\MergePullRequestProcess;
use MergePullRequestPm\Domain\MergePullRequestState;
use MergePullRequestPm\Domain\UseCase\MoneyCollected;
use MergePullRequestPm\Domain\UseCase\MoneyCollectedHandler;
use Soa\ProcessManager\Domain\InvalidStateTransitionException;
use Soa\ProcessManager\Domain\Transition;
use Soa\ProcessManager\Testing\ProcessManagerTestCase;

class MoneyCollectedHandlerTest extends ProcessManagerTestCase
{
    public function setUp()
    {
        $this->scenario->withDomainEventHandler(new MoneyCollectedHandler());
    }

    /**
     * @test
     */
    public function shouldRemovePendingPaymentWhenMoneyIsCollected()
    {
        $transactionId        = 'collect money';
        $anotherTransactionId = 'pay money';
        $process              = (new MergePullRequestProcess())
            ->withId('process id')
            ->withPullRequestId('pull request id')
            ->withPendingPayments([$transactionId, $anotherTransactionId])
            ->withState(MergePullRequestState::EXCHANGING_MONEY());

        $this->scenario
            ->given($process)
            ->when((new MoneyCollected())->withStreamId($transactionId))
            ->then(Transition::to($process->withPendingPayments([$anotherTransactionId])));
    }

    /**
     * @test
     */
    public function shouldTransitToMerging_when_allPaymentsDone()
    {
        $transactionId = 'collect money';
        $pullRequestId = 'pull request id';
        $process       = (new MergePullRequestProcess())
            ->withPendingPayments([$transactionId])
            ->withPullRequestId($pullRequestId)
            ->withId('process id')
            ->withState(MergePullRequestState::EXCHANGING_MONEY());

        $this->scenario
            ->given($process)
            ->when((new MoneyCollected())->withStreamId($transactionId))
            ->then(
                Transition::to($process->withState(MergePullRequestState::MERGING())->withPendingPayments([]))
                    ->withCommands([
                        (new MergePullRequestCommand($pullRequestId))
                            ->withStreamId($pullRequestId)
                            ->withProcessId($process->id())
                            ->withRecipient(RecipientAddress::PULL_REQUEST), ]
                    )
            );
    }

    /**
     * @test
     */
    public function shouldFail_when_processNotExchangingMoney()
    {
        $transactionId = 'collect money';
        $pullRequestId = 'pull request id';
        $process       = (new MergePullRequestProcess())
            ->withPendingPayments([$transactionId])
            ->withPullRequestId($pullRequestId)
            ->withId('process id')
            ->withState(MergePullRequestState::EXCHANGING_MONEY());

        $possibleStates = MergePullRequestState::getConstList();
        foreach ($possibleStates as $possibleState) {
            if (MergePullRequestState::EXCHANGING_MONEY()->getName() !== $possibleState) {
                $this->scenario
                    ->given($process->withState(MergePullRequestState::$possibleState()))
                    ->expectException(InvalidStateTransitionException::class)
                    ->when((new MoneyCollected())->withStreamId('a stream id'));
            }
        }
    }
}
