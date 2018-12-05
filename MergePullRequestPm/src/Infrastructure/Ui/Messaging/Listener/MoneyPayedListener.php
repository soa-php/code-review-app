<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Ui\Messaging\Listener;

use function Martinezdelariva\Hydrator\hydrate;
use MergePullRequestPm\Application\MergePullRequestEventBus;
use MergePullRequestPm\Domain\UseCase\MoneyPayed;
use Soa\MessageStore\Message;

class MoneyPayedListener extends AbstractMessageListener
{
    public function handleMessage(Message $message): void
    {
        $event = $this->event($message)->withProcessId($message->processId())->withStreamId($message->streamId());

        $this->eventBus(MergePullRequestEventBus::class)->handle($event);
    }

    private function event(Message $message): MoneyPayed
    {
        return $event = hydrate(MoneyPayed::class, $message->body());
    }
}
