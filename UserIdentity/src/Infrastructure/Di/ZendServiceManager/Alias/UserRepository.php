<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Di\ZendServiceManager\Alias;

use Soa\EventSourcing\Repository\Repository;
use UserIdentity\Domain\User;

interface UserRepository extends Repository
{
    public function findOfRefreshToken(string $token): ?User;
}
