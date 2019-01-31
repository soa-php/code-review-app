<?php

declare(strict_types=1);

namespace UserIdentity\Domain\UseCase;

use Soa\EventSourcing\Command\ConventionBasedCommand;

class LogUserInWithPasswordCommand extends ConventionBasedCommand
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $email;

    /**
     * @var array
     */
    private $roles;

    public function __construct(string $id, string $username, string $password, string $email, array $roles)
    {
        $this->username        = $username;
        $this->password        = $password;
        $this->email           = $email;
        $this->aggregateRootId = $id;
        $this->roles           = $roles;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function roles(): array
    {
        return $this->roles;
    }
}
