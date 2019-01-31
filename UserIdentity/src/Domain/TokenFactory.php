<?php

declare(strict_types=1);

namespace UserIdentity\Domain;

interface TokenFactory
{
    public function createAccessToken(string $userId, array $userRoles): string;

    public function createRefreshToken(string $userId, array $userRoles): string;
}
