<?php

declare(strict_types=1);

namespace UserIdentity\Domain;

interface PasswordEncryption
{
    public function encrypt(string $password): string;

    public function isValid(string $encryptedPassword, string $plainPassword): bool;
}
