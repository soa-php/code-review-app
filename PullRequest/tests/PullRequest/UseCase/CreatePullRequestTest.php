<?php

declare(strict_types=1);

namespace PullRequestTest\UseCase;

use PullRequest\Application\Projection\PullRequestProjector;
use PullRequest\Domain\Event\PullRequestCreated;
use PullRequest\Domain\Event\PullRequestCreationFailed;
use PullRequest\Domain\UseCase\CreatePullRequestCommand;
use PullRequest\Domain\UseCase\CreatePullRequestCommandHandler;
use Soa\EventSourcing\Testing\CommandHandlerTestCase;

class CreatePullRequestTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->scenario->withCommandHandler(new CreatePullRequestCommandHandler());
        $this->scenario->withProjector(new PullRequestProjector());
    }

    /**
     * @test
     */
    public function shouldCreatePullRequest()
    {
        $code       = 'some code';
        $writer     = 'some writer';
        $id         = 'some id';

        $this->scenario
            ->when((new CreatePullRequestCommand($code, $writer))->withAggregateRootId($id))
            ->then(new PullRequestCreated($id, $writer, $code))
            ->andProjection([
                'id'                => $id,
                'writer'            => $writer,
                'code'              => $code,
                'assignedReviewers' => [],
                'approvers'         => [],
                'mergeable'         => false,
                'merged'            => false,
        ]);
    }

    /**
     * @test
     */
    public function shouldFail_when_emptyCode()
    {
        $code          = '';
        $writer        = 'some writer';
        $failureReason = PullRequestCreationFailed::EMPTY_CODE;

        $this->scenario
            ->when(new CreatePullRequestCommand($code, $writer))
            ->then(new PullRequestCreationFailed($writer, $code, $failureReason));
    }

    /**
     * @test
     */
    public function shouldFail_when_emptyWriter()
    {
        $code          = 'some code';
        $writer        = '';
        $failureReason = PullRequestCreationFailed::EMPTY_WRITER;

        $this->scenario
            ->when(new CreatePullRequestCommand($code, $writer))
            ->then(new PullRequestCreationFailed($writer, $code, $failureReason));
    }
}
