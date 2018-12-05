<?php

declare(strict_types=1);

namespace PullRequest\Domain\UseCase;

use Soa\EventSourcing\Command\Command;
use Soa\EventSourcing\Command\CommandHandler;
use Soa\EventSourcing\Event\EventStream;
use Soa\EventSourcing\Repository\AggregateRoot;
use PullRequest\Domain\Event\PullRequestReviewerAssignationFailed;
use PullRequest\Domain\Event\PullRequestReviewerAssigned;
use PullRequest\Domain\PullRequest;

class AssignPullRequestReviewerCommandHandler implements CommandHandler
{
    /**
     * @param AssignPullRequestReviewerCommand $command
     * @param PullRequest                      $aggregateRoot
     */
    public function handle(Command $command, AggregateRoot $aggregateRoot): EventStream
    {
        if (!$command->reviewer()) {
            return EventStream::fromDomainEvents(new PullRequestReviewerAssignationFailed($command->aggregateRootId(), $command->reviewer(), PullRequestReviewerAssignationFailed::EMPTY_REVIEWER));
        }

        if (2 === count($aggregateRoot->assignedReviewers())) {
            return EventStream::fromDomainEvents(new PullRequestReviewerAssignationFailed($command->aggregateRootId(), $command->reviewer(), PullRequestReviewerAssignationFailed::MAX_REVIEWERS_ASSIGNED));
        }

        if (in_array($command->reviewer(), $aggregateRoot->assignedReviewers())) {
            return EventStream::fromDomainEvents(new PullRequestReviewerAssignationFailed($command->aggregateRootId(), $command->reviewer(), PullRequestReviewerAssignationFailed::REVIEWER_ALREADY_ASSIGNED));
        }

        return EventStream::fromDomainEvents(new PullRequestReviewerAssigned($command->aggregateRootId(), $command->reviewer()));
    }
}
