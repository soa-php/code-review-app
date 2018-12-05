<?php

declare(strict_types=1);

namespace Payment\Domain\Event;

use Soa\EventSourcing\Event\DomainEvent;

class MoneyPayed implements DomainEvent
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $payee;

    /**
     * @var string
     */
    private $amount;

    /**
     * @var string
     */
    private $currencyCode;

    /**
     * @var string
     */
    private $subjectId;

    /**
     * @var string
     */
    private $provider;

    /**
     * @var string
     */
    private $providerTransactionId;

    public function __construct(string $id, string $payee, string $amount, string $currencyCode, string $subjectId, string $provider, string $providerTransactionId)
    {
        $this->id                    = $id;
        $this->payee                 = $payee;
        $this->amount                = $amount;
        $this->currencyCode          = $currencyCode;
        $this->subjectId             = $subjectId;
        $this->provider              = $provider;
        $this->providerTransactionId = $providerTransactionId;
    }

    public function streamId(): string
    {
        return $this->id;
    }

    public function payee(): string
    {
        return $this->payee;
    }

    public function amount(): string
    {
        return $this->amount;
    }

    public function currencyCode(): string
    {
        return $this->currencyCode;
    }

    public function subjectId(): string
    {
        return $this->subjectId;
    }

    public function provider(): string
    {
        return $this->provider;
    }

    public function providerTransactionId(): string
    {
        return $this->providerTransactionId;
    }
}
