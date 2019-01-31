<?php

declare(strict_types=1);

namespace UserIdentity\Domain\UseCase;

use Soa\EventSourcing\Command\Command;
use Soa\EventSourcing\Command\CommandHandler;
use Soa\EventSourcing\Event\EventStream;
use Soa\EventSourcing\Repository\AggregateRoot;
use UserIdentity\Domain\Event\LogUserInWithPasswordFailed;
use UserIdentity\Domain\Event\UserWithPasswordLoggedIn;
use UserIdentity\Domain\PasswordEncryption;
use UserIdentity\Domain\TokenFactory;
use UserIdentity\Domain\UserWithPasswordContentValidator;

class LogUserInWithPasswordCommandHandler implements CommandHandler
{
    /**
     * @var TokenFactory
     */
    private $tokenBuilder;

    /**
     * @var PasswordEncryption
     */
    private $passwordEncryption;

    /**
     * @var UserWithPasswordContentValidator
     */
    private $contentValidator;

    public function __construct(
        TokenFactory $tokenBuilder,
        PasswordEncryption $passwordEncryption,
        UserWithPasswordContentValidator $contentValidator
    ) {
        $this->tokenBuilder       = $tokenBuilder;
        $this->passwordEncryption = $passwordEncryption;
        $this->contentValidator   = $contentValidator;
    }

    /**
     * @param LogUserInWithPasswordCommand $command
     */
    public function handle(Command $command, AggregateRoot $aggregateRoot = null): EventStream
    {
        $validationResult = $this->contentValidator->validate($command);

        if (!$validationResult->wasSucceed()) {
            return EventStream::fromDomainEvents(
                LogUserInWithPasswordFailed::withReason($command->aggregateRootId(), $validationResult->failureReason())
            );
        }

        return EventStream::fromDomainEvents(new UserWithPasswordLoggedIn(
            $command->aggregateRootId(),
            $command->username(),
            $this->passwordEncryption->encrypt($command->password()),
            $command->email(),
            $command->roles(),
            $this->tokenBuilder->createAccessToken($command->aggregateRootId(), $command->roles()),
            $this->tokenBuilder->createRefreshToken($command->aggregateRootId(), $command->roles())
        ));
    }
}
