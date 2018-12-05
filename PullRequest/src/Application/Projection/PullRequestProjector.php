<?php

declare(strict_types=1);

namespace PullRequest\Application\Projection;

use PullRequest\Domain\Event\ApprovePullRequestFailed;
use PullRequest\Domain\Event\PullRequestApproved;
use PullRequest\Domain\Event\PullRequestCreated;
use PullRequest\Domain\Event\PullRequestCreationFailed;
use PullRequest\Domain\Event\PullRequestMarkedAsMergeable;
use PullRequest\Domain\Event\PullRequestMerged;
use PullRequest\Domain\Event\PullRequestReviewerAssignationFailed;
use PullRequest\Domain\Event\PullRequestReviewerAssigned;
use PullRequest\Domain\Event\MergePullRequestFailed;
use Soa\EventSourcing\Projection\ConventionBasedProjector;

class PullRequestProjector extends ConventionBasedProjector
{
    public function projectPullRequestCreated(PullRequestCreated $event, array $projection): array
    {
        $projection['id']                = $event->streamId();
        $projection['writer']            = $event->writer();
        $projection['code']              = $event->code();
        $projection['assignedReviewers'] = [];
        $projection['approvers']         = [];
        $projection['mergeable']         = false;
        $projection['merged']            = false;

        return $projection;
    }

    public function projectPullRequestReviewerAssigned(PullRequestReviewerAssigned $event, array $projection): array
    {
        $projection['assignedReviewers'][] = $event->reviewer();

        return $projection;
    }

    public function projectPullRequestApproved(PullRequestApproved $event, array $projection): array
    {
        $projection['approvers'][] = $event->approver();

        return $projection;
    }

    public function projectPullRequestMarkedAsMergeable(PullRequestMarkedAsMergeable $event, array $projection): array
    {
        $projection['mergeable']   = true;

        return $projection;
    }

    public function projectPullRequestMerged(PullRequestMerged $event, array $projection): array
    {
        $projection['merged']      = true;

        return $projection;
    }

    public function projectMergePullRequestFailed(MergePullRequestFailed $event, array $projection): array
    {
        return $projection;
    }

    public function projectPullRequestCreationFailed(PullRequestCreationFailed $event, array $projection): array
    {
        return $projection;
    }

    public function projectPullRequestReviewerAssignationFailed(PullRequestReviewerAssignationFailed $event, array $projection): array
    {
        return $projection;
    }

    public function projectApprovePullRequestFailed(ApprovePullRequestFailed $event, array $projection): array
    {
        return $projection;
    }
}
