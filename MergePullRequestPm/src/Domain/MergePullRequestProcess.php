<?php

declare(strict_types=1);

namespace MergePullRequestPm\Domain;

use Soa\ProcessManager\Domain\Process;

class MergePullRequestProcess extends Process
{
    /**
     * @var string
     */
    private $pullRequestId;

    /**
     * @var string[]
     */
    private $pendingPayments;

    public function pullRequestId(): string
    {
        return $this->pullRequestId;
    }

    public function withPullRequestId(string $pullRequestId): self
    {
        $clone                = clone $this;
        $clone->pullRequestId = $pullRequestId;

        return $clone;
    }

    public function pendingPayments(): array
    {
        return $this->pendingPayments;
    }

    public function withPendingPayments(array $pendingPayments): self
    {
        $clone                  = clone $this;
        $clone->pendingPayments = $pendingPayments;

        return $clone;
    }
}
