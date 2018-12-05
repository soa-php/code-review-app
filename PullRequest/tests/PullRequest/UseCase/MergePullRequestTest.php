<?php

declare(strict_types=1);

namespace PullRequestTest\UseCase;

use PullRequest\Application\Projection\PullRequestProjector;
use PullRequest\Domain\Event\MergePullRequestFailed;
use PullRequest\Domain\Event\PullRequestMerged;
use PullRequest\Domain\PullRequest;
use PullRequest\Domain\UseCase\MergePullRequestCommand;
use PullRequest\Domain\UseCase\MergePullRequestCommandHandler;
use Soa\EventSourcing\Testing\CommandHandlerTestCase;

class MergePullRequestTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->scenario->withCommandHandler(new MergePullRequestCommandHandler());
        $this->scenario->withProjector(new PullRequestProjector());
    }

    /**
     * @test
     */
    public function shouldMerge()
    {
        $id             = 'e0b5b77f-3e19-4002-b710-8a89c6c64836';
        $pullRequest    = (new PullRequest($id))->withMergeable(true);

        $this->scenario
            ->given($pullRequest)
            ->when(new MergePullRequestCommand($id))
            ->then(new PullRequestMerged($id))
            ->andProjection([
                'merged' => true,
            ]);
    }

    /**
     * @test
     */
    public function shouldFail_when_pullRequestNotMergeable()
    {
        $id             = 'e0b5b77f-3e19-4002-b710-8a89c6c64836';
        $pullRequest    = (new PullRequest($id))->withMergeable(false);
        $failureReason  = MergePullRequestFailed::NOT_MERGEABLE;

        $this->scenario
            ->given($pullRequest)
            ->when(new MergePullRequestCommand($id))
            ->then(new MergePullRequestFailed($id, $failureReason));
    }

    /**
     * @test
     */
    public function shouldFail_when_pullRequestAlreadyMerged()
    {
        $id             = 'e0b5b77f-3e19-4002-b710-8a89c6c64836';
        $pullRequest    = (new PullRequest($id))->withMergeable(true)->withMerged(true);
        $failureReason  = MergePullRequestFailed::ALREADY_MERGED;

        $this->scenario
            ->given($pullRequest)
            ->when(new MergePullRequestCommand($id))
            ->then(new MergePullRequestFailed($id, $failureReason));
    }
}
