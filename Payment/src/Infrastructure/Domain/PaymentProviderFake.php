<?php

declare(strict_types=1);

namespace Payment\Infrastructure\Domain;

use Payment\Domain\PaymentProvider;
use Payment\Domain\Transaction;

class PaymentProviderFake implements PaymentProvider
{
    public const ID = 'fake payment provider id';

    /**
     * @var Transaction
     */
    private $transaction;

    public static function withTransaction(Transaction $transaction): self
    {
        return new self($transaction);
    }

    public function __construct(Transaction $transaction = null)
    {
        $this->transaction = empty($transaction) ? Transaction::succeed('transaction id') : $transaction;
    }

    public function collect(string $payer, string $amount, string $currencyCode, string $subjectId): Transaction
    {
        return $this->transaction;
    }

    public function payout(string $payee, string $amount, string $currencyCode, string $subjectId): Transaction
    {
        return $this->transaction;
    }

    public function identifier(): string
    {
        return self::ID;
    }
}
