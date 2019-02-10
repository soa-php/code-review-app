<?php

declare(strict_types=1);

namespace UserIdentity\Domain\UseCase;

use Common\Ui\Http\Restful\Authorization\TokenFactory;
use Soa\EventSourcing\Command\Command;
use Soa\EventSourcing\Command\CommandHandler;
use Soa\EventSourcing\Event\EventStream;
use Soa\EventSourcing\Repository\AggregateRoot;
use UserIdentity\Domain\Event\LogUserInWithPasswordFailed;
use UserIdentity\Domain\Event\UserWithPasswordLoggedIn;
use UserIdentity\Domain\PasswordEncryption;
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
    public function handle(Command $command, AggregateRoot $user = null): EventStream
    {
        $validationResult = $this->contentValidator->validate($command);

        if (!$validationResult->wasSucceed()) {
            return EventStream::fromDomainEvents(
                LogUserInWithPasswordFailed::withReason($command->userId(), $validationResult->failureReason())
            );
        }

        return EventStream::fromDomainEvents(new UserWithPasswordLoggedIn(
            $command->userId(),
            $command->username(),
            $this->passwordEncryption->encrypt($command->password()),
            $command->email(),
            $command->roles(),
            $this->tokenBuilder->createAccessToken($command->userId(), $command->roles()),
            $this->tokenBuilder->createRefreshToken($command->userId(), $command->roles())
        ));
    }
}
