<?php

declare(strict_types=1);

namespace MergePullRequestPm\Domain\Provider;

class PullRequest
{
    /**
     * @var string
     */
    private $writer;

    /**
     * @var array
     */
    private $reviewers;

    /**
     * @var string
     */
    private $pullRequestId;

    public function __construct(string $writer, string $pullRequestId, array $reviewers)
    {
        $this->writer        = $writer;
        $this->pullRequestId = $pullRequestId;
        $this->reviewers     = $reviewers;
    }

    public function writer(): string
    {
        return $this->writer;
    }

    public function pullRequestId(): string
    {
        return $this->pullRequestId;
    }

    public function reviewers(): array
    {
        return $this->reviewers;
    }
}
