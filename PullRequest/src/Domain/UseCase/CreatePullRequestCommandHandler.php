<?php

declare(strict_types=1);

namespace PullRequest\Domain\UseCase;

use Soa\EventSourcing\Command\Command;
use Soa\EventSourcing\Command\CommandHandler;
use Soa\EventSourcing\Event\EventStream;
use Soa\EventSourcing\Repository\AggregateRoot;
use PullRequest\Domain\Event\PullRequestCreated;
use PullRequest\Domain\Event\PullRequestCreationFailed;
use PullRequest\Domain\PullRequest;

class CreatePullRequestCommandHandler implements CommandHandler
{
    /**
     * @param CreatePullRequestCommand $command
     * @param PullRequest              $aggregateRoot
     */
    public function handle(Command $command, AggregateRoot $aggregateRoot = null): EventStream
    {
        if (!$command->code()) {
            return EventStream::fromDomainEvents(new PullRequestCreationFailed($command->writer(), $command->code(), PullRequestCreationFailed::EMPTY_CODE));
        }

        if (!$command->writer()) {
            return EventStream::fromDomainEvents(new PullRequestCreationFailed($command->writer(), $command->code(), PullRequestCreationFailed::EMPTY_WRITER));
        }

        return EventStream::fromDomainEvents(new PullRequestCreated($command->aggregateRootId(), $command->writer(), $command->code()));
    }
}
