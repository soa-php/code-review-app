<?php

declare(strict_types=1);

namespace UserIdentityTest\UserIdentity\Double;

use UserIdentity\Domain\PasswordEncryption;

class PasswordEncryptionStub implements PasswordEncryption
{
    /**
     * @var string
     */
    private $encryptedPassword;

    /**
     * @var bool
     */
    private $isValid;

    public function __construct(string $encryptedPassword, bool $isValid)
    {
        $this->encryptedPassword = $encryptedPassword;
        $this->isValid           = $isValid;
    }

    public function encrypt(string $password): string
    {
        return $this->encryptedPassword;
    }

    public function isValid(string $encryptedPassword, string $plainPassword): bool
    {
        return $this->isValid;
    }
}
