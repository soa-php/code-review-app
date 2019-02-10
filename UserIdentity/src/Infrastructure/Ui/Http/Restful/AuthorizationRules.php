<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Ui\Http\Restful;

use Common\Ui\Http\Restful\Authorization\AuthorizationType;
use UserIdentity\Infrastructure\Ui\Http\Restful\Resource\UserAccessTokenResource;
use UserIdentity\Infrastructure\Ui\Http\Restful\Resource\UserWithPasswordCollectionResource;

class AuthorizationRules
{
    public static function getRules(): array
    {
        return [
            UserAccessTokenResource::class => [
                AuthorizationType::PUT_METHOD => ['writer', 'reviewer'],
            ],
            UserWithPasswordCollectionResource::class => [
                AuthorizationType::POST_METHOD => AuthorizationType::NO_AUTH,
            ],
        ];
    }
}
