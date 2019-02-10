<?php

declare(strict_types=1);

namespace Payment\Infrastructure\Ui\Messaging\Listener;

use Common\Ui\Messaging\Listener\AbstractMessageListener;
use function Martinezdelariva\Functional\match;
use function Martinezdelariva\Hydrator\hydrate;
use Payment\Application\PaymentCommandBus;
use Payment\Domain\UseCase\PayMoneyCommand;
use Soa\EventSourcing\Command\CommandResponse;
use Soa\EventSourcing\Event\FailureDomainEvent;
use Soa\MessageStore\Message;

class PayMoneyCommandListener extends AbstractMessageListener
{
    public function handle(Message $message): void
    {
        $command = hydrate(PayMoneyCommand::class, $message->body());

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
