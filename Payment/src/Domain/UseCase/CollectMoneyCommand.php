<?php

declare(strict_types=1);

namespace Payment\Domain\UseCase;

use Soa\EventSourcing\Command\ConventionBasedCommand;

class CollectMoneyCommand extends ConventionBasedCommand
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
    private $subjectId;

    /**
     * @var string
     */
    private $currencyCode;

    public function __construct(string $payer, string $amount, string $currencyCode, string $subjectId)
    {
        $this->payer         = $payer;
        $this->amount        = $amount;
        $this->subjectId     = $subjectId;
        $this->currencyCode  = $currencyCode;
    }

    public function payer(): string
    {
        return $this->payer;
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
