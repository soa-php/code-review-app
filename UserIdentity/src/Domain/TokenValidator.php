<?php

declare(strict_types=1);

namespace UserIdentity\Domain;

interface TokenValidator
{
    public function isValid(string $token): bool;
}
