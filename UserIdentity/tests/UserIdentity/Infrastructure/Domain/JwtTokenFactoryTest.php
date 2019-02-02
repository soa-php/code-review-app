<?php

declare(strict_types=1);

namespace UserIdentityTest\UserIdentity\Infrastructure\Domain;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use Soa\Clock\ClockFake;
use UserIdentity\Infrastructure\Domain\JwtTokenFactory;
use PHPUnit\Framework\TestCase;

class JwtTokenFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCreateTokens()
    {
        $config    = require __DIR__ . '/../../../../src/Infrastructure/Di/ZendServiceManager/autoload/config.global.php';
        $jwtConfig = $config['jwt'];

        $now       = '2019-01-01 00:00:00';
        $nowTime   = new \DateTimeImmutable($now);
        $factory   = new JwtTokenFactory($jwtConfig, new Sha256(), new ClockFake($now));
        $userId    = 'some user id';
        $userRoles = ['role1', ['role2']];

        $accessToken  = $factory->createAccessToken($userId, $userRoles);
        $refreshToken = $factory->createRefreshToken($userId, $userRoles);

        $parsedAccessToken  = (new Parser())->parse($accessToken);
        $parsedRefreshToken = (new Parser())->parse($refreshToken);

        $this->assertTokenContent($parsedAccessToken, $userRoles, $userId, $nowTime, $jwtConfig[JwtTokenFactory::ACCESS_TOKEN_EXPIRATION]);
        $this->assertTokenContent($parsedRefreshToken, $userRoles, $userId, $nowTime, $jwtConfig[JwtTokenFactory::REFRESH_TOKEN_EXPIRATION]);
    }

    private function assertTokenContent(
        Token $parsedAccessToken,
        array $userRoles,
        string $userId,
        \DateTimeImmutable $nowTime,
        int $expirationTime
    ): void {
        $this->assertEquals($parsedAccessToken->getClaim(JwtTokenFactory::CLAIM_ROLES), $userRoles);
        $this->assertEquals($parsedAccessToken->getClaim(JwtTokenFactory::CLAIM_USER_ID), $userId);
        $this->assertFalse($parsedAccessToken->isExpired($nowTime->add(\DateInterval::createFromDateString($expirationTime . ' seconds'))));
        $this->assertTrue($parsedAccessToken->isExpired($nowTime->add(\DateInterval::createFromDateString(($expirationTime + 1) . ' seconds'))));
    }
}
