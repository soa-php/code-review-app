<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Ui\Messaging\Listener;

use function Martinezdelariva\Hydrator\hydrate;
use MergePullRequestPm\Application\MergePullRequestEventBus;
use MergePullRequestPm\Domain\UseCase\PullRequestMerged;
use Soa\MessageStore\Message;

class PullRequestMergedListener extends AbstractMessageListener
{
    public function handleMessage(Message $message): void
    {
        $event = $this->event($message)->withProcessId($message->processId())->withStreamId($message->streamId());

        $this->eventBus(MergePullRequestEventBus::class)->handle($event);
    }

    private function event(Message $message): PullRequestMerged
    {
        return $event = hydrate(PullRequestMerged::class, $message->body());
    }
}
