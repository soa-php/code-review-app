<?php

declare(strict_types=1);

namespace PaymentTest\UseCase;

use Payment\Application\Projection\PaymentProjector;
use Payment\Domain\Event\CollectMoneyFailed;
use Payment\Domain\Event\MoneyCollected;
use Payment\Domain\Transaction;
use Payment\Domain\UseCase\CollectMoneyCommand;
use Payment\Domain\UseCase\CollectMoneyCommandHandler;
use Payment\Infrastructure\Domain\PaymentProviderFake;
use Soa\EventSourcing\Testing\CommandHandlerTestCase;

class CollectMoneyTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->scenario->withProjector(new PaymentProjector());
    }

    /**
     * @test
     */
    public function shouldCollectMoney()
    {
        $id                    = 'some id';
        $payer                 = 'some payer';
        $amount                = '100';
        $currencyCode          = 'EUR';
        $subjectId             = 'subject id';
        $provider              = PaymentProviderFake::ID;
        $providerTransactionId = 'some id';

        $this->scenario
            ->withCommandHandler(new CollectMoneyCommandHandler(PaymentProviderFake::withTransaction(Transaction::succeed($id))))
            ->when((new CollectMoneyCommand($payer, $amount, $currencyCode, $subjectId))->withAggregateRootId($id))
            ->then(new MoneyCollected($id, $payer, $amount, $currencyCode, $subjectId, $provider, $providerTransactionId))
            ->andProjection([
                    'id'                    => $id,
                    'payer'                 => $payer,
                    'amount'                => $amount,
                    'currencyCode'          => $currencyCode,
                    'subjectId'             => $subjectId,
                    'provider'              => $provider,
                    'providerTransactionId' => $providerTransactionId,
                    'status'                => 'success',
                ]
            );
    }

    /**
     * @test
     */
    public function shouldFail_when_providerReturnsFailedTransaction()
    {
        $id                    = 'some id';
        $payer                 = 'some payer';
        $amount                = '-100';
        $currencyCode          = 'EUR';
        $subjectId             = 'subject id';
        $provider              = PaymentProviderFake::ID;
        $providerTransactionId = $id;
        $failureReason         = 'negative amount';

        $this->scenario
            ->withCommandHandler(new CollectMoneyCommandHandler(PaymentProviderFake::withTransaction(Transaction::failed($id, $failureReason))))
            ->when((new CollectMoneyCommand($payer, $amount, $currencyCode, $subjectId))->withAggregateRootId($id))
            ->then(new CollectMoneyFailed($id, $payer, $amount, $currencyCode, $subjectId, $provider, $providerTransactionId, $failureReason))
            ->andProjection([
                    'id'                    => $id,
                    'payer'                 => $payer,
                    'amount'                => $amount,
                    'currencyCode'          => $currencyCode,
                    'subjectId'             => $subjectId,
                    'provider'              => $provider,
                    'providerTransactionId' => $providerTransactionId,
                    'reason'                => $failureReason,
                    'status'                => 'failure',
                ]
            );
    }
}
