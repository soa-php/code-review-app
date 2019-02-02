<?php

declare(strict_types=1);

namespace PullRequest\Infrastructure\Ui\Messaging\Listener;

use Common\Ui\Messaging\Listener\AbstractMessageListener;
use function Martinezdelariva\Functional\match;
use function Martinezdelariva\Hydrator\hydrate;
use PullRequest\Application\PullRequestCommandBus;
use PullRequest\Domain\UseCase\MergePullRequestCommand;
use Soa\EventSourcing\Command\CommandResponse;
use Soa\EventSourcing\Event\FailureDomainEvent;
use Soa\MessageStore\Message;

class MergePullRequestCommandListener extends AbstractMessageListener
{
    public function handle(Message $message): void
    {
        $command = hydrate(MergePullRequestCommand::class, $message->body());

        /** @var CommandResponse $result */
        $result = $this->commandBus(PullRequestCommandBus::class, $message)->handle($command);

        $pattern = [
            FailureDomainEvent::class => function (FailureDomainEvent $event) {
                throw new \Exception($event->reason());
            },
        ];

        match($pattern, $result->eventStream()->first());
    }
}
