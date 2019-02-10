<?php

declare(strict_types=1);

namespace Common\Ui\Http\Restful\Authorization;

class AuthorizationType
{
    public const POST_METHOD   = 'POST';
    public const GET_METHOD    = 'GET';
    public const PATCH_METHOD  = 'PATCH';
    public const PUT_METHOD    = 'PUT';
    public const DELETE_METHOD = 'DELETE';

    public const NO_AUTH = 'no auth';
}
