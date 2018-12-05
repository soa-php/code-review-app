<?php

declare(strict_types=1);

namespace MergePullRequestPmTest\UseCase;

use MergePullRequestPm\Domain\MergePullRequestProcess;
use MergePullRequestPm\Domain\MergePullRequestState;
use MergePullRequestPm\Domain\UseCase\PullRequestMerged;
use MergePullRequestPm\Domain\UseCase\PullRequestMergedHandler;
use Soa\ProcessManager\Domain\Transition;
use Soa\ProcessManager\Testing\ProcessManagerTestCase;

class PullRequestMergedHandlerTest extends ProcessManagerTestCase
{
    public function setUp()
    {
        $this->scenario->withDomainEventHandler(new PullRequestMergedHandler());
    }

    /**
     * @test
     */
    public function shouldTransitToFinished(): void
    {
        $process = new MergePullRequestProcess();
        $this->scenario
            ->given($process)
            ->when((new PullRequestMerged())->withStreamId('some stream id'))
            ->then(Transition::to($process->withState(MergePullRequestState::FINISHED()))->withCommands([]));
    }
}
