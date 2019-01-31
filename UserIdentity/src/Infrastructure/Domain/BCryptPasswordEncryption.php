<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Domain;

use UserIdentity\Domain\PasswordEncryption;

class BCryptPasswordEncryption implements PasswordEncryption
{
    public function encrypt(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function isValid(string $encryptedPassword, string $plainPassword): bool
    {
        return password_verify($plainPassword, $encryptedPassword);
    }
}
