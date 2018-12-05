<?php

declare(strict_types=1);

namespace MergePullRequestPm\Domain\Command;

use Soa\ProcessManager\Domain\Command;

class PayMoneyCommand extends Command
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

    /**
     * @var string
     */
    private $aggregateRootId;

    public function __construct(string $aggregateRootId, string $payee, string $amount, string $currencyCode, string $subjectId)
    {
        $this->payee           = $payee;
        $this->amount          = $amount;
        $this->subjectId       = $subjectId;
        $this->currencyCode    = $currencyCode;
        $this->aggregateRootId = $aggregateRootId;
    }
}
