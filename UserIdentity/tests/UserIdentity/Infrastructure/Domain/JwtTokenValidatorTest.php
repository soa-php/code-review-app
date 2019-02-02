<?php

declare(strict_types=1);

namespace UserIdentityTest\UserIdentity\Infrastructure\Domain;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use UserIdentity\Infrastructure\Domain\JwtTokenFactory;
use UserIdentity\Infrastructure\Domain\JwtTokenValidator;
use PHPUnit\Framework\TestCase;

class JwtTokenValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function should_return_true()
    {
        $validator = new JwtTokenValidator($this->jwtConfig(), new Sha256());
        $token     = $this->createTestToken(5, $this->jwtConfig());
        $this->assertTrue($validator->isValid($token));
    }

    /**
     * @test
     */
    public function should_return_false_when_token_modified()
    {
        $validator = new JwtTokenValidator($this->jwtConfig(), new Sha256());
        $token     = $this->createTestToken(5, $this->jwtConfig());
        $token .= 'a';
        $this->assertFalse($validator->isValid($token));
    }

    /**
     * @test
     */
    public function should_return_false_when_token_expired()
    {
        $validator = new JwtTokenValidator($this->jwtConfig(), new Sha256());
        $token     = $this->createTestToken(-1, $this->jwtConfig());
        $this->assertFalse($validator->isValid($token));
    }

    /**
     * @test
     */
    public function should_return_false_when_token_signed_with_different_key()
    {
        $jwtConfig = $this->jwtConfig();

        $validator                       = new JwtTokenValidator($jwtConfig, new Sha256());
        $jwtConfig[JwtTokenFactory::KEY] = 'another secret';
        $token                           = $this->createTestToken(5, $jwtConfig);
        $this->assertFalse($validator->isValid($token));
    }

    private function createTestToken(int $expireInSeconds, array $jwtConfig): string
    {
        $builder = new Builder();
        $builder
            ->setExpiration(time() + $expireInSeconds)
            ->sign(new Sha256(), $jwtConfig[JwtTokenFactory::KEY]);

        return (string) $builder->getToken();
    }

    private function jwtConfig(): array
    {
        $config    = require __DIR__ . '/../../../../src/Infrastructure/Di/ZendServiceManager/autoload/config.global.php';
        $jwtConfig = $config['jwt'];

        return $jwtConfig;
    }
}
