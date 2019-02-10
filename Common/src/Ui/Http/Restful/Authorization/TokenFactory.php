<?php

declare(strict_types=1);

namespace Common\Ui\Http\Restful\Authorization;

interface TokenFactory
{
    public function createAccessToken(string $userId, array $userRoles): string;

    public function createRefreshToken(string $userId, array $userRoles): string;
}
