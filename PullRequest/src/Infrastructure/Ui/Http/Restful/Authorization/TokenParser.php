<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Ui\Http\Restful\Authorization;

interface TokenParser
{
    public function parse(string $token): Token;
}
