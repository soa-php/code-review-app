<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Ui\Http\Restful\Authorization;

class Token
{
    /**
     * @var string
     */
    private $userId;

    /**
     * @var array
     */
    private $roles;

    public function __construct(string $userId, array $roles)
    {
        $this->userId = $userId;
        $this->roles  = $roles;
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function roles(): array
    {
        return $this->roles;
    }
}
