<?php

declare(strict_types=1);

namespace UserIdentity\Domain\UseCase;

use Common\Ui\Http\Restful\Authorization\TokenFactory;
use Common\Ui\Http\Restful\Authorization\TokenValidator;
use Soa\EventSourcing\Command\Command;
use Soa\EventSourcing\Command\CommandHandler;
use Soa\EventSourcing\Event\EventStream;
use Soa\EventSourcing\Repository\AggregateRoot;
use UserIdentity\Domain\Event\UserAccessTokenRefreshed;
use UserIdentity\Domain\Event\RefreshUserAccessTokenFailed;
use UserIdentity\Domain\User;

class RefreshUserAccessTokenCommandHandler implements CommandHandler
{
    /**
     * @var TokenFactory
     */
    private $tokenFactory;

    /**
     * @var TokenValidator
     */
    private $tokenValidator;

    public function __construct(TokenValidator $tokenValidator, TokenFactory $tokenFactory)
    {
        $this->tokenFactory   = $tokenFactory;
        $this->tokenValidator = $tokenValidator;
    }

    /**
     * @param User                          $user
     * @param RefreshUserAccessTokenCommand $command
     */
    public function handle(Command $command, AggregateRoot $user): EventStream
    {
        if (!$this->tokenValidator->isValid($user->refreshToken())) {
            return EventStream::fromDomainEvents(RefreshUserAccessTokenFailed::becauseGivenRefreshTokenIsInvalid($user->id(), $user->refreshToken()));
        }

        return EventStream::fromDomainEvents(
            new UserAccessTokenRefreshed($user->id(), $this->tokenFactory->createAccessToken($user->id(), $user->roles()))
        );
    }
}
