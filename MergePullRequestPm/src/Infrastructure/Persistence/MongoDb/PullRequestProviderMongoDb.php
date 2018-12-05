<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Persistence\MongoDb;

use MergePullRequestPm\Domain\Provider\PullRequest;
use MergePullRequestPm\Domain\Provider\PullRequestProvider;
use MongoDB\Collection;

class PullRequestProviderMongoDb implements PullRequestProvider
{
    /**
     * @var Collection
     */
    private $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function ofId(string $pullRequestId): PullRequest
    {
        $result = $this->collection->findOne(['id' => $pullRequestId]);

        if (empty($result)) {
            throw new \RuntimeException("Pull Request $pullRequestId not found");
        }

        return new PullRequest($result['writer'], $result['id'], $result['approvers']);
    }
}
