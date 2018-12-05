<?php

declare(strict_types=1);

namespace PaymentTest\UseCase;

use Payment\Application\Projection\PaymentProjector;
use Payment\Domain\Event\MoneyPayed;
use Payment\Domain\Event\PayMoneyFailed;
use Payment\Domain\Transaction;
use Payment\Domain\UseCase\PayMoneyCommand;
use Payment\Domain\UseCase\PayMoneyCommandHandler;
use Payment\Infrastructure\Domain\PaymentProviderFake;
use Soa\EventSourcing\Testing\CommandHandlerTestCase;

class PayMoneyTest extends CommandHandlerTestCase
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
        $payee                 = 'some payee';
        $amount                = '100';
        $currencyCode          = 'EUR';
        $subjectId             = 'subject id';
        $provider              = PaymentProviderFake::ID;
        $providerTransactionId = 'some id';

        $this->scenario
            ->withCommandHandler(new PayMoneyCommandHandler(PaymentProviderFake::withTransaction(Transaction::succeed($id))))
            ->when((new PayMoneyCommand($payee, $amount, $currencyCode, $subjectId))->withAggregateRootId($id))
            ->then(new MoneyPayed($id, $payee, $amount, $currencyCode, $subjectId, $provider, $providerTransactionId))
            ->andProjection([
                    'id'                    => $id,
                    'payee'                 => $payee,
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
        $payee                 = 'some payee';
        $amount                = '-100';
        $currencyCode          = 'EUR';
        $subjectId             = 'subject id';
        $provider              = PaymentProviderFake::ID;
        $providerTransactionId = 'some id';
        $failureReason         = 'negative amount';

        $this->scenario
            ->withCommandHandler(new PayMoneyCommandHandler(PaymentProviderFake::withTransaction(Transaction::failed($id, $failureReason))))
            ->when((new PayMoneyCommand($payee, $amount, $currencyCode, $subjectId))->withAggregateRootId($id))
            ->then(new PayMoneyFailed($id, $payee, $amount, $currencyCode, $subjectId, $provider, $providerTransactionId, $failureReason))
            ->andProjection([
                    'id'                    => $id,
                    'payee'                 => $payee,
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
