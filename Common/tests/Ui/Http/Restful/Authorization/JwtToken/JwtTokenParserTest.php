<?php

declare(strict_types=1);

namespace CommonTest\Ui\Http\Restful\Authorization\JwtToken;

use Common\Ui\Http\Restful\Authorization\Token;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use PHPUnit\Framework\TestCase;

class JwtTokenParserTest extends TestCase
{
    /**
     * @test
     */
    public function should_parse()
    {
        $userId    = 'some id';
        $userRoles = ['role 1', 'role 2'];

        $token = (string) (new Builder())
            ->set(Token::USER_ID_CLAIM, $userId)
            ->set(Token::USER_ROLES_CLAIM, $userRoles)
            ->getToken();

        $jwtToken = (new Parser())->parse($token);

        $this->assertEquals($userId, $jwtToken->getClaim(Token::USER_ID_CLAIM));
        $this->assertEquals($userRoles, $jwtToken->getClaim(Token::USER_ROLES_CLAIM));
    }
}
