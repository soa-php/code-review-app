<?php

declare(strict_types=1);

namespace PullRequest\Domain;

use Soa\EventSourcing\Repository\AggregateRoot;

class PullRequest implements AggregateRoot
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string[]
     */
    private $assignedReviewers;

    /**
     * @var array
     */
    private $approvers;

    /**
     * @var bool
     */
    private $mergeable;

    /**
     * @var bool
     */
    private $merged;

    public function __construct(string $id, bool $mergeable = false, array $reviewers = [], array $approvers = [], bool $merged = false)
    {
        $this->id                  = $id;
        $this->assignedReviewers   = $reviewers;
        $this->approvers           = $approvers;
        $this->mergeable           = $mergeable;
        $this->merged              = $merged;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function mergeable(): bool
    {
        return $this->mergeable;
    }

    public function assignedReviewers(): array
    {
        return $this->assignedReviewers;
    }

    public function approvers(): array
    {
        return $this->approvers;
    }

    public function merged(): bool
    {
        return $this->merged;
    }

    public function withApprovers(array $approvers): self
    {
        $clone                      = clone $this;
        $clone->approvers           = $approvers;

        return $clone;
    }

    public function withAssignedReviewers(array $assignedReviewers): self
    {
        $clone                    = clone $this;
        $clone->assignedReviewers = $assignedReviewers;

        return $clone;
    }

    public function withMergeable(bool $mergeable): self
    {
        $clone                    = clone $this;
        $clone->mergeable         = $mergeable;

        return $clone;
    }

    public function withMerged(bool $merged): self
    {
        $clone         = clone $this;
        $clone->merged = $merged;

        return $clone;
    }
}
