<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Persistence\InMemory;

use MergePullRequestPm\Domain\Provider\PullRequest;
use MergePullRequestPm\Domain\Provider\PullRequestProvider;

class PullRequestProviderInMemory implements PullRequestProvider
{
    public function ofId(string $pullRequestId): PullRequest
    {
        return new PullRequest('writer', 'pull request id', ['reviewer']);
    }
}
