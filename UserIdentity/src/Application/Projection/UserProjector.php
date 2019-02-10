<?php

declare(strict_types=1);

namespace UserIdentity\Application\Projection;

use Soa\EventSourcing\Projection\ConventionBasedProjector;
use UserIdentity\Domain\Event\LogUserInWithPasswordFailed;
use UserIdentity\Domain\Event\RefreshUserAccessTokenFailed;
use UserIdentity\Domain\Event\UserAccessTokenRefreshed;
use UserIdentity\Domain\Event\UserWithPasswordLoggedIn;

class UserProjector extends ConventionBasedProjector
{
    public function projectUserWithPasswordLoggedIn(UserWithPasswordLoggedIn $event, array $projection): array
    {
        $projection['id']           = $event->streamId();
        $projection['username']     = $event->username();
        $projection['email']        = $event->email();
        $projection['password']     = $event->password();
        $projection['accessToken']  = $event->accessToken();
        $projection['refreshToken'] = $event->refreshToken();
        $projection['roles']        = $event->roles();

        return $projection;
    }

    public function projectLogUserInWithPasswordFailed(LogUserInWithPasswordFailed $event, array $projection): array
    {
        return $projection;
    }

    public function projectUserAccessTokenRefreshed(UserAccessTokenRefreshed $event, array $projection): array
    {
        $projection['accessToken'] = $event->accessToken();

        return $projection;
    }

    public function projectRefreshUserAccessTokenFailed(RefreshUserAccessTokenFailed $event, array $projection): array
    {
        return $projection;
    }
}
