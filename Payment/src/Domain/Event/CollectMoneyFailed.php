<?php

declare(strict_types=1);

namespace Payment\Domain\Event;

use Soa\EventSourcing\Event\DomainEvent;
use Soa\EventSourcing\Event\FailureDomainEvent;

class CollectMoneyFailed implements DomainEvent, FailureDomainEvent
{
    /**
     * @var string
     */
    private $payer;

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

    /**
     * @var string
     */
    private $reason;

    /**
     * @var string
     */
    private $id;

    public function __construct(string $id, string $payer, string $amount, string $currencyCode, string $subjectId, string $provider, string $providerTransactionId, string $reason)
    {
        $this->id                    = $id;
        $this->payer                 = $payer;
        $this->amount                = $amount;
        $this->currencyCode          = $currencyCode;
        $this->subjectId             = $subjectId;
        $this->provider              = $provider;
        $this->providerTransactionId = $providerTransactionId;
        $this->reason                = $reason;
    }

    public function streamId(): string
    {
        return $this->id;
    }

    public function payer(): string
    {
        return $this->payer;
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

    public function reason(): string
    {
        return $this->reason;
    }
}
