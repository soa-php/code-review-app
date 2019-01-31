<?php

declare(strict_types=1);

namespace UserIdentityTest\UseCase;

use Soa\EventSourcing\Testing\CommandHandlerTestCase;
use UserIdentity\Application\Projection\UserProjector;
use UserIdentity\Domain\ContentValidationResult;
use UserIdentity\Domain\Event\LogUserInWithPasswordFailed;
use UserIdentity\Domain\Event\UserWithPasswordLoggedIn;
use UserIdentity\Domain\UseCase\LogUserInWithPasswordCommand;
use UserIdentity\Domain\UseCase\LogUserInWithPasswordCommandHandler;
use UserIdentityTest\UserIdentity\Double\PasswordEncryptionStub;
use UserIdentityTest\UserIdentity\Double\TokenFactoryStub;
use UserIdentityTest\UserIdentity\Double\UserWithPasswordContentValidatorStub;

class LogUserInWithPasswordCommandHandlerTest extends CommandHandlerTestCase
{
    /**
     * @test
     */
    public function shouldLogUserInWithPassword()
    {
        $id                      = 'some id';
        $username                = 'a username';
        $password                = 'a password';
        $email                   = 'a email';
        $roles                   = ['some role'];
        $accessToken             = 'some access token';
        $refreshToken            = 'some refresh token';
        $encryptedPassword       = 'encrypted password';
        $contentValidationResult = ContentValidationResult::succeed();

        $this->scenario->withCommandHandler(new LogUserInWithPasswordCommandHandler(
            TokenFactoryStub::withTokens($accessToken, $refreshToken),
            new PasswordEncryptionStub($encryptedPassword, true),
            new UserWithPasswordContentValidatorStub($contentValidationResult)
        ));

        $this->scenario->withProjector(new UserProjector());

        $this->scenario
            ->when(new LogUserInWithPasswordCommand(
                $id,
                $username,
                $password,
                $email,
                $roles
            ))
            ->then(new UserWithPasswordLoggedIn(
                $id,
                $username,
                $encryptedPassword,
                $email,
                $roles,
                $accessToken,
                $refreshToken
            ))
            ->andProjection(
                [
                    'id'           => $id,
                    'username'     => $username,
                    'password'     => $encryptedPassword,
                    'email'        => $email,
                    'roles'        => $roles,
                    'accessToken'  => $accessToken,
                    'refreshToken' => $refreshToken,
                ]
            );
    }

    /**
     * @test
     */
    public function shouldFail_when_invalidContentGiven()
    {
        $id                      = 'some id';
        $username                = 'a username';
        $password                = 'a password';
        $email                   = 'a email';
        $roles                   = ['some role'];
        $accessToken             = 'some access token';
        $refreshToken            = 'some refresh token';
        $encryptedPassword       = 'encrypted password';
        $failureReason           = 'failure reason';
        $contentValidationResult = ContentValidationResult::failed($failureReason);

        $this->scenario->withCommandHandler(new LogUserInWithPasswordCommandHandler(
            TokenFactoryStub::withTokens($accessToken, $refreshToken),
            new PasswordEncryptionStub($encryptedPassword, true),
            new UserWithPasswordContentValidatorStub($contentValidationResult)
        ));

        $this->scenario
            ->when(new LogUserInWithPasswordCommand(
                $id,
                $username,
                $password,
                $email,
                $roles
            ))
            ->then(LogUserInWithPasswordFailed::withReason($id, $failureReason));
    }
}
