<?php

declare(strict_types=1);

namespace Common\Ui\Http\Restful\Authorization;

class Token
{
    public const USER_ID_CLAIM    = 'user-id';
    public const TOKEN_TYPE_CLAIM = 'token-type';
    public const REFRESH_TYPE     = 'refresh';
    public const ACCESS_TYPE      = 'access';
    public const USER_ROLES_CLAIM = 'roles';

    /**
     * @var string
     */
    private $userId;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var string
     */
    private $type;

    public function __construct(string $userId, array $roles, string $type)
    {
        $this->userId = $userId;
        $this->roles  = $roles;
        $this->type   = $type;
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function roles(): array
    {
        return $this->roles;
    }

    public function type(): string
    {
        return $this->type;
    }
}
