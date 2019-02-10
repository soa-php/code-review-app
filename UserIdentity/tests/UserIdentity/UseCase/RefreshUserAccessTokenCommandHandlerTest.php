<?php

declare(strict_types=1);

namespace UserIdentityTest\UseCase;

use Soa\EventSourcing\Testing\CommandHandlerTestCase;
use UserIdentity\Application\Projection\UserProjector;
use UserIdentity\Domain\Event\RefreshUserAccessTokenFailed;
use UserIdentity\Domain\Event\UserAccessTokenRefreshed;
use UserIdentity\Domain\UseCase\RefreshUserAccessTokenCommand;
use UserIdentity\Domain\UseCase\RefreshUserAccessTokenCommandHandler;
use UserIdentity\Domain\User;
use UserIdentityTest\UserIdentity\Double\TokenFactoryStub;
use UserIdentityTest\UserIdentity\Double\TokenValidatorStub;

class RefreshUserAccessTokenCommandHandlerTest extends CommandHandlerTestCase
{
    /**
     * @test
     */
    public function shouldRefreshUserAccessToken()
    {
        $newAccessToken             = 'new access token';
        $refreshToken               = 'valid refresh token';
        $aUserWithValidRefreshToken = (new User('some id'))->withRefreshToken($refreshToken);

        $this->scenario->withCommandHandler(new RefreshUserAccessTokenCommandHandler(
            TokenValidatorStub::withValidToken(),
            TokenFactoryStub::withTokens($newAccessToken, $refreshToken)
        ));

        $this->scenario->withProjector(new UserProjector());

        $this->scenario
            ->given($aUserWithValidRefreshToken)
            ->when((new RefreshUserAccessTokenCommand())->withAggregateRootId($aUserWithValidRefreshToken->id()))
            ->then(new UserAccessTokenRefreshed($aUserWithValidRefreshToken->id(), $newAccessToken))
            ->andProjection(
                [
                    'accessToken'  => $newAccessToken,
                ]
            );
    }

    /**
     * @test
     */
    public function shouldFail_when_invalidRefreshTokenGiven()
    {
        $newAccessToken               = 'new access token';
        $invalidRefreshToken          = 'invalid refresh token';
        $aUserWithInvalidRefreshToken = (new User('some id'))->withRefreshToken($invalidRefreshToken);

        $this->scenario->withCommandHandler(new RefreshUserAccessTokenCommandHandler(
            TokenValidatorStub::withInvalidToken(),
            TokenFactoryStub::withTokens($newAccessToken, $invalidRefreshToken)
        ));

        $this->scenario->withProjector(new UserProjector());

        $this->scenario
            ->given($aUserWithInvalidRefreshToken)
            ->when((new RefreshUserAccessTokenCommand())->withAggregateRootId($aUserWithInvalidRefreshToken->id()))
            ->then(RefreshUserAccessTokenFailed::becauseGivenRefreshTokenIsInvalid($aUserWithInvalidRefreshToken->id(), $invalidRefreshToken));
    }
}
