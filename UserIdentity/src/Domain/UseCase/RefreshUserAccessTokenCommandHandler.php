<?php

declare(strict_types=1);

namespace UserIdentity\Domain\UseCase;

use Soa\EventSourcing\Command\Command;
use Soa\EventSourcing\Command\CommandHandler;
use Soa\EventSourcing\Event\EventStream;
use Soa\EventSourcing\Repository\AggregateRoot;
use UserIdentity\Domain\Event\UserAccessTokenRefreshed;
use UserIdentity\Domain\Event\RefreshUserAccessTokenFailed;
use UserIdentity\Domain\TokenFactory;
use UserIdentity\Domain\TokenValidator;
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
     * @param User                          $aggregateRoot
     * @param RefreshUserAccessTokenCommand $command
     */
    public function handle(Command $command, AggregateRoot $aggregateRoot): EventStream
    {
        if (!$this->tokenValidator->isValid($aggregateRoot->refreshToken())) {
            return EventStream::fromDomainEvents(RefreshUserAccessTokenFailed::becauseGivenRefreshTokenIsInvalid($aggregateRoot->id(), $aggregateRoot->refreshToken()));
        }

        return EventStream::fromDomainEvents(
            new UserAccessTokenRefreshed($aggregateRoot->id(), $this->tokenFactory->createAccessToken($aggregateRoot->id(), $aggregateRoot->roles()))
        );
    }
}
