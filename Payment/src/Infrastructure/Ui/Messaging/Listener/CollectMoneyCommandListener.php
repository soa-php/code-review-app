<?php

declare(strict_types=1);

namespace Payment\Infrastructure\Ui\Messaging\Listener;

use function Martinezdelariva\Functional\match;
use function Martinezdelariva\Hydrator\hydrate;
use Payment\Application\PaymentCommandBus;
use Payment\Domain\UseCase\CollectMoneyCommand;
use Soa\EventSourcing\Command\CommandResponse;
use Soa\EventSourcing\Event\FailureDomainEvent;
use Soa\MessageStore\Message;

class CollectMoneyCommandListener extends AbstractMessageListener
{
    public function handle(Message $message): void
    {
        $command = hydrate(CollectMoneyCommand::class, $message->body());

        /** @var CommandResponse $result */
        $result = $this->commandBus(PaymentCommandBus::class, $message)->handle($command);

        $pattern = [
            FailureDomainEvent::class => function (FailureDomainEvent $event) {
                throw new \Exception($event->reason());
            },
        ];

        match($pattern, $result->eventStream()->first());
    }
}
