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
     * @param PullRequest               $pullRequest
     */
    public function handle(Command $command, AggregateRoot $pullRequest): EventStream
    {
        if ($pullRequest->mergeable()) {
            return EventStream::fromDomainEvents(new ApprovePullRequestFailed($command->pullRequestId(), $command->approver(), ApprovePullRequestFailed::ALREADY_MARKED_AS_MERGEABLE));
        }

        if (!in_array($command->approver(), $pullRequest->assignedReviewers())) {
            return EventStream::fromDomainEvents(new ApprovePullRequestFailed($command->pullRequestId(), $command->approver(), ApprovePullRequestFailed::APPROVER_IS_NOT_REVIEWER));
        }

        if (in_array($command->approver(), $pullRequest->approvers())) {
            return EventStream::fromDomainEvents(new ApprovePullRequestFailed($command->pullRequestId(), $command->approver(), ApprovePullRequestFailed::APPROVER_ALREADY_APPROVED));
        }

        $events[] = new PullRequestApproved($command->pullRequestId(), $command->approver());

        $approvalsRequired = 2;
        if (count($pullRequest->approvers()) + 1 === $approvalsRequired) {
            $events[] = new PullRequestMarkedAsMergeable($command->pullRequestId());
        }

        return EventStream::fromDomainEvents(...$events);
    }
}
