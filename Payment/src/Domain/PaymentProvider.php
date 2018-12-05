<?php

declare(strict_types=1);

namespace Payment\Domain;

interface PaymentProvider
{
    public function collect(string $payer, string $amount, string $currencyCode, string $subjectId): Transaction;

    public function payout(string $payee, string $amount, string $currencyCode, string $subjectId): Transaction;

    public function identifier(): string;
}
