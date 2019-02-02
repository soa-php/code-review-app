<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Ui\Http\Restful\Authorization\TokenParser;

use Lcobucci\JWT\Parser;
use PullRequest\Infrastructure\Ui\Http\Restful\Authorization\Token;
use PullRequest\Infrastructure\Ui\Http\Restful\Authorization\TokenParser;

class JwtTokenParser implements TokenParser
{
    public function parse(string $token): Token
    {
        $token = (new Parser())->parse($token);

        return new Token($token->getClaim('user_id'), $token->getClaim('roles'));
    }
}
