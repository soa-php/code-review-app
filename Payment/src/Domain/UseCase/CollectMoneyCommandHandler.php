<?php

declare(strict_types=1);

namespace Payment\Domain\UseCase;

use Payment\Domain\Event\CollectMoneyFailed;
use Payment\Domain\Event\MoneyCollected;
use Payment\Domain\PaymentProvider;
use Soa\EventSourcing\Command\Command;
use Soa\EventSourcing\Command\CommandHandler;
use Soa\EventSourcing\Event\EventStream;
use Soa\EventSourcing\Repository\AggregateRoot;

class CollectMoneyCommandHandler implements CommandHandler
{
    /**
     * @var PaymentProvider
     */
    private $provider;

    public function __construct(PaymentProvider $provider)
    {
        $this->provider   = $provider;
    }

    /**
     * @param CollectMoneyCommand $command
     */
    public function handle(Command $command, AggregateRoot $state = null): EventStream
    {
        $transaction = $this->provider->collect($command->payer(), $command->amount(), $command->currencyCode(), $command->subjectId());
        if ($transaction->wasSucceed()) {
            return EventStream::fromDomainEvents(new MoneyCollected(
                $command->aggregateRootId(),
                $command->payer(),
                $command->amount(),
                $command->currencyCode(),
                $command->subjectId(),
                $this->provider->identifier(),
                $transaction->id())
            );
        }

        return EventStream::fromDomainEvents(new CollectMoneyFailed(
                $command->aggregateRootId(),
                $command->payer(),
                $command->amount(),
                $command->currencyCode(),
                $command->subjectId(),
                $this->provider->identifier(),
                $transaction->id(),
                $transaction->failureReason()
        ));
    }
}
