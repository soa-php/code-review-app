<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Ui\Http\Restful;

use PullRequest\Infrastructure\Ui\Http\Restful\Resource\PullRequestApproveResource;
use PullRequest\Infrastructure\Ui\Http\Restful\Resource\PullRequestCollectionResource;
use PullRequest\Infrastructure\Ui\Http\Restful\Resource\PullRequestReviewerResource;

class AuthorizationRules
{
    private const POST = 'POST';
    private const PUT  = 'PUT';

    public static function getRules(): array
    {
        return [
            PullRequestCollectionResource::class => [
                self::POST => ['writer'],
            ],
            PullRequestReviewerResource::class => [
                self::PUT => ['writer'],
            ],
            PullRequestApproveResource::class => [
                self::PUT => ['reviewer'],
            ],
        ];
    }
}
