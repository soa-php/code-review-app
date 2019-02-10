<?php

declare(strict_types=1);

namespace CommonTest\Ui\Http\Restful\Authorization\JwtToken;

use Common\Ui\Http\Restful\Authorization\JwtToken\JwtTokenFactory;
use Common\Ui\Http\Restful\Authorization\Token;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token as JwtToken;
use Soa\Clock\ClockFake;
use PHPUnit\Framework\TestCase;

class JwtTokenFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCreateTokens()
    {
        $jwtConfig = [
            JwtTokenFactory::ISSUER                   => 'user_identity_bc',
            JwtTokenFactory::ACCESS_TOKEN_EXPIRATION  => 900,
            JwtTokenFactory::REFRESH_TOKEN_EXPIRATION => 86400,
            JwtTokenFactory::KEY                      => 'some random key',
        ];

        $now       = '2019-01-01 00:00:00';
        $nowTime   = new \DateTimeImmutable($now);
        $factory   = new JwtTokenFactory($jwtConfig, new Sha256(), new ClockFake($now));
        $userId    = 'some user id';
        $userRoles = ['role1', ['role2']];

        $accessToken  = $factory->createAccessToken($userId, $userRoles);
        $refreshToken = $factory->createRefreshToken($userId, $userRoles);

        $parsedAccessToken  = (new Parser())->parse($accessToken);
        $parsedRefreshToken = (new Parser())->parse($refreshToken);

        $this->assertTokenContent($parsedAccessToken, $userRoles, $userId, $nowTime, $jwtConfig[JwtTokenFactory::ACCESS_TOKEN_EXPIRATION], Token::ACCESS_TYPE);
        $this->assertTokenContent($parsedRefreshToken, $userRoles, $userId, $nowTime, $jwtConfig[JwtTokenFactory::REFRESH_TOKEN_EXPIRATION], Token::REFRESH_TYPE);
    }

    private function assertTokenContent(
        JwtToken $parsedAccessToken,
        array $userRoles,
        string $userId,
        \DateTimeImmutable $nowTime,
        int $expirationTime,
        string $tokenType
    ): void {
        $this->assertEquals($parsedAccessToken->getClaim(Token::USER_ROLES_CLAIM), $userRoles);
        $this->assertEquals($parsedAccessToken->getClaim(Token::USER_ID_CLAIM), $userId);
        $this->assertEquals($parsedAccessToken->getClaim(Token::TOKEN_TYPE_CLAIM), $tokenType);
        $this->assertFalse($parsedAccessToken->isExpired($nowTime->add(\DateInterval::createFromDateString($expirationTime . ' seconds'))));
        $this->assertTrue($parsedAccessToken->isExpired($nowTime->add(\DateInterval::createFromDateString(($expirationTime + 1) . ' seconds'))));
    }
}
