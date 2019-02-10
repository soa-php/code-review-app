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
     * @param PullRequest                      $pullRequest
     */
    public function handle(Command $command, AggregateRoot $pullRequest): EventStream
    {
        if (!$command->reviewer()) {
            return EventStream::fromDomainEvents(new PullRequestReviewerAssignationFailed($command->pullRequestId(), $command->reviewer(), PullRequestReviewerAssignationFailed::EMPTY_REVIEWER));
        }

        if (2 === count($pullRequest->assignedReviewers())) {
            return EventStream::fromDomainEvents(new PullRequestReviewerAssignationFailed($command->pullRequestId(), $command->reviewer(), PullRequestReviewerAssignationFailed::MAX_REVIEWERS_ASSIGNED));
        }

        if (in_array($command->reviewer(), $pullRequest->assignedReviewers())) {
            return EventStream::fromDomainEvents(new PullRequestReviewerAssignationFailed($command->pullRequestId(), $command->reviewer(), PullRequestReviewerAssignationFailed::REVIEWER_ALREADY_ASSIGNED));
        }

        return EventStream::fromDomainEvents(new PullRequestReviewerAssigned($command->pullRequestId(), $command->reviewer()));
    }
}
