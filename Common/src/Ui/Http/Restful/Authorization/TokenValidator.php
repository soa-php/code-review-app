<?php

declare(strict_types=1);

namespace Common\Ui\Http\Restful\Authorization;

interface TokenValidator
{
    public function isValid(string $token): bool;
}
