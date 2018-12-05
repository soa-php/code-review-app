<?php

declare(strict_types=1);

namespace Payment\Domain\UseCase;

use Soa\EventSourcing\Command\ConventionBasedCommand;

class PayMoneyCommand extends ConventionBasedCommand
{
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
    private $subjectId;

    /**
     * @var string
     */
    private $currencyCode;

    public function __construct(string $payee, string $amount, string $currencyCode, string $subjectId)
    {
        $this->payee         = $payee;
        $this->amount        = $amount;
        $this->subjectId     = $subjectId;
        $this->currencyCode  = $currencyCode;
    }

    public function payee(): string
    {
        return $this->payee;
    }

    public function amount(): string
    {
        return $this->amount;
    }

    public function subjectId(): string
    {
        return $this->subjectId;
    }

    public function currencyCode(): string
    {
        return $this->currencyCode;
    }
}
