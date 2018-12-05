<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Ui\Messaging\Listener;

use function Martinezdelariva\Hydrator\hydrate;
use MergePullRequestPm\Application\MergePullRequestEventBus;
use MergePullRequestPm\Domain\UseCase\PullRequestMarkedAsMergeable;
use Soa\MessageStore\Message;

class PullRequestMarkedAsMergeableListener extends AbstractMessageListener
{
    public function handleMessage(Message $message): void
    {
        $event = $this->event($message)->withProcessId($message->processId())->withStreamId($message->streamId());

        $this->eventBus(MergePullRequestEventBus::class)->handle($event);
    }

    private function event(Message $message): PullRequestMarkedAsMergeable
    {
        return $event = hydrate(PullRequestMarkedAsMergeable::class, $message->body());
    }
}
