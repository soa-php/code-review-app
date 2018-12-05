<?php

declare(strict_types=1);

namespace PullRequestTest\UseCase;

use PullRequest\Application\Projection\PullRequestProjector;
use PullRequest\Domain\Event\ApprovePullRequestFailed;
use PullRequest\Domain\Event\PullRequestApproved;
use PullRequest\Domain\Event\PullRequestMarkedAsMergeable;
use PullRequest\Domain\PullRequest;
use PullRequest\Domain\UseCase\ApprovePullRequestCommand;
use PullRequest\Domain\UseCase\ApprovePullRequestCommandHandler;
use Soa\EventSourcing\Testing\CommandHandlerTestCase;

class ApprovePullRequestTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->scenario->withCommandHandler(new ApprovePullRequestCommandHandler());
        $this->scenario->withProjector(new PullRequestProjector());
    }

    /**
     * @test
     */
    public function shouldApprovePullRequest()
    {
        $id             = 'e0b5b77f-3e19-4002-b710-8a89c6c64836';
        $anApprover     = $aReviewer     = 'a reviewer';
        $pullRequest    = (new PullRequest($id))->withAssignedReviewers([$aReviewer]);

        $this->scenario
            ->given($pullRequest)
            ->when(new ApprovePullRequestCommand($id, $anApprover))
            ->then(new PullRequestApproved($id, $anApprover))
            ->andProjection([
                'approvers' => [$anApprover],
            ]);
    }

    /**
     * @test
     */
    public function shouldMarkAsMergeable_when_approvedByAllReviewers()
    {
        $id              = 'e0b5b77f-3e19-4002-b710-8a89c6c64836';
        $anApprover      = $aReviewer      = 'a reviewer';
        $anotherApprover = $anotherReviewer = 'another approver';
        $pullRequest     = (new PullRequest($id))
                            ->withAssignedReviewers([$aReviewer, $anotherReviewer])
                            ->withApprovers([$anApprover]);

        $this->scenario
            ->given($pullRequest)
            ->when(new ApprovePullRequestCommand($id, $anotherApprover))
            ->then(new PullRequestApproved($id, $anotherApprover), new PullRequestMarkedAsMergeable($id))
            ->andProjection([
                'approvers' => [$anotherApprover],
                'mergeable' => true,
            ]);
    }

    /**
     * @test
     */
    public function shouldFail_when_approverIsNotReviewer()
    {
        $id               = 'e0b5b77f-3e19-4002-b710-8a89c6c64836';
        $aReviewer        = 'another approver';
        $anApprover       = 'an approver';
        $pullRequest      = (new PullRequest($id))->withAssignedReviewers([$aReviewer]);
        $failureReason    = ApprovePullRequestFailed::APPROVER_IS_NOT_REVIEWER;

        $this->scenario
            ->given($pullRequest)
            ->when(new ApprovePullRequestCommand($id, $anApprover))
            ->then(new ApprovePullRequestFailed($id, $anApprover, $failureReason));
    }

    /**
     * @test
     */
    public function shouldFail_when_alreadyMarkedAsMergeable()
    {
        $id             = 'e0b5b77f-3e19-4002-b710-8a89c6c64836';
        $aReviewer      = $anApprover            = 'some approver';
        $pullRequest    = (new PullRequest($id))
                            ->withAssignedReviewers([$aReviewer])
                            ->withMergeable(true);
        $failureReason = ApprovePullRequestFailed::ALREADY_MARKED_AS_MERGEABLE;

        $this->scenario
            ->given($pullRequest)
            ->when(new ApprovePullRequestCommand($id, $anApprover))
            ->then(new ApprovePullRequestFailed($id, $anApprover, $failureReason));
    }

    /**
     * @test
     */
    public function shouldFail_when_approverAlreadyApproved()
    {
        $id             = 'e0b5b77f-3e19-4002-b710-8a89c6c64836';
        $aReviewer      = $anApprover            = 'some approver';
        $pullRequest    = (new PullRequest($id))
                            ->withAssignedReviewers([$aReviewer])
                            ->withApprovers([$anApprover]);
        $failureReason = ApprovePullRequestFailed::APPROVER_ALREADY_APPROVED;

        $this->scenario
            ->given($pullRequest)
            ->when(new ApprovePullRequestCommand($id, $anApprover))
            ->then(new ApprovePullRequestFailed($id, $anApprover, $failureReason));
    }
}
