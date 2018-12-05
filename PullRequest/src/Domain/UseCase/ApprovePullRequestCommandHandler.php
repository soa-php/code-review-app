<?php

declare(strict_types=1);

namespace PullRequest\Domain\UseCase;

use Soa\EventSourcing\Command\Command;
use Soa\EventSourcing\Command\CommandHandler;
use Soa\EventSourcing\Event\EventStream;
use Soa\EventSourcing\Repository\AggregateRoot;
use PullRequest\Domain\Event\ApprovePullRequestFailed;
use PullRequest\Domain\Event\PullRequestApproved;
use PullRequest\Domain\Event\PullRequestMarkedAsMergeable;
use PullRequest\Domain\PullRequest;

class ApprovePullRequestCommandHandler implements CommandHandler
{
    /**
     * @param ApprovePullRequestCommand $command
     * @param PullRequest               $aggregateRoot
     */
    public function handle(Command $command, AggregateRoot $aggregateRoot): EventStream
    {
        if ($aggregateRoot->mergeable()) {
            return EventStream::fromDomainEvents(new ApprovePullRequestFailed($command->aggregateRootId(), $command->approver(), ApprovePullRequestFailed::ALREADY_MARKED_AS_MERGEABLE));
        }

        if (!in_array($command->approver(), $aggregateRoot->assignedReviewers())) {
            return EventStream::fromDomainEvents(new ApprovePullRequestFailed($command->aggregateRootId(), $command->approver(), ApprovePullRequestFailed::APPROVER_IS_NOT_REVIEWER));
        }

        if (in_array($command->approver(), $aggregateRoot->approvers())) {
            return EventStream::fromDomainEvents(new ApprovePullRequestFailed($command->aggregateRootId(), $command->approver(), ApprovePullRequestFailed::APPROVER_ALREADY_APPROVED));
        }

        $events[] = new PullRequestApproved($command->aggregateRootId(), $command->approver());

        $approvalsRequired = 2;
        if (count($aggregateRoot->approvers()) + 1 === $approvalsRequired) {
            $events[] = new PullRequestMarkedAsMergeable($command->aggregateRootId());
        }

        return EventStream::fromDomainEvents(...$events);
    }
}
