<?php

declare(strict_types=1);

namespace PullRequestTest\UseCase;

use PullRequest\Application\Projection\PullRequestProjector;
use PullRequest\Domain\Event\PullRequestReviewerAssignationFailed;
use PullRequest\Domain\Event\PullRequestReviewerAssigned;
use PullRequest\Domain\PullRequest;
use PullRequest\Domain\UseCase\AssignPullRequestReviewerCommand;
use PullRequest\Domain\UseCase\AssignPullRequestReviewerCommandHandler;
use Soa\EventSourcing\Testing\CommandHandlerTestCase;

class AssignReviewerTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->scenario->withCommandHandler(new AssignPullRequestReviewerCommandHandler());
        $this->scenario->withProjector(new PullRequestProjector());
    }

    /**
     * @test
     */
    public function shouldAssignReviewer()
    {
        $id             = 'e0b5b77f-3e19-4002-b710-8a89c6c64836';
        $reviewer       = 'some reviewer';
        $pullRequest    = new PullRequest($id);

        $this->scenario
            ->given($pullRequest)
            ->when(new AssignPullRequestReviewerCommand($id, $reviewer))
            ->then(new PullRequestReviewerAssigned($id, $reviewer))
            ->andProjection([
                'assignedReviewers' => [$reviewer],
            ]);
    }

    /**
     * @test
     */
    public function shouldFail_when_reviewerWasAlreadyAssigned()
    {
        $id             = 'e0b5b77f-3e19-4002-b710-8a89c6c64836';
        $reviewer       = 'some reviewer';
        $pullRequest    = (new PullRequest($id))->withAssignedReviewers([$reviewer]);
        $failureReason  = PullRequestReviewerAssignationFailed::REVIEWER_ALREADY_ASSIGNED;

        $this->scenario
            ->given($pullRequest)
            ->when(new AssignPullRequestReviewerCommand($id, $reviewer))
            ->then(new PullRequestReviewerAssignationFailed($id, $reviewer, $failureReason));
    }

    /**
     * @test
     */
    public function shouldFail_when_maxReviewersAssigned()
    {
        $id                       = 'e0b5b77f-3e19-4002-b710-8a89c6c64836';
        $newReviewer              = 'some reviewer';
        $alreadyAssignedReviewers = ['another reviewer', 'another one reviewer'];
        $pullRequest              = (new PullRequest($id))->withAssignedReviewers($alreadyAssignedReviewers);
        $failureReason            = PullRequestReviewerAssignationFailed::MAX_REVIEWERS_ASSIGNED;

        $this->scenario
            ->given($pullRequest)
            ->when(new AssignPullRequestReviewerCommand($id, $newReviewer))
            ->then(new PullRequestReviewerAssignationFailed($id, $newReviewer, $failureReason));
    }

    /**
     * @test
     */
    public function shouldFail_when_emptyReviewer()
    {
        $id             = 'e0b5b77f-3e19-4002-b710-8a89c6c64836';
        $reviewer       = '';
        $pullRequest    = new PullRequest($id);
        $failureReason  = PullRequestReviewerAssignationFailed::EMPTY_REVIEWER;

        $this->scenario
            ->given($pullRequest)
            ->when(new AssignPullRequestReviewerCommand($id, $reviewer))
            ->then(new PullRequestReviewerAssignationFailed($id, $reviewer, $failureReason));
    }
}
