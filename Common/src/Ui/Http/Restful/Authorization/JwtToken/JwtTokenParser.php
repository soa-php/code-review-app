<?php

declare(strict_types=1);

namespace Common\Ui\Http\Restful\Authorization\JwtToken;

use Lcobucci\JWT\Parser;
use Common\Ui\Http\Restful\Authorization\Token;
use Common\Ui\Http\Restful\Authorization\TokenParser;

class JwtTokenParser implements TokenParser
{
    public function parse(string $token): Token
    {
        $token = (new Parser())->parse($token);

        return new Token(
            $token->getClaim(Token::USER_ID_CLAIM),
            $token->getClaim(Token::USER_ROLES_CLAIM),
            $token->getClaim(Token::TOKEN_TYPE_CLAIM)
        );
    }
}
