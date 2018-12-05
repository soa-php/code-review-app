<?php

declare(strict_types=1);

namespace MergePullRequestPm\Domain\Provider;

interface PullRequestProvider
{
    public function ofId(string $pullRequestId): PullRequest;
}
