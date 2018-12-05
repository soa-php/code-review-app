<?php

declare(strict_types=1);

namespace Payment\Domain\UseCase;

use Payment\Domain\Event\MoneyPayed;
use Payment\Domain\Event\PayMoneyFailed;
use Payment\Domain\PaymentProvider;
use Soa\EventSourcing\Command\Command;
use Soa\EventSourcing\Command\CommandHandler;
use Soa\EventSourcing\Event\EventStream;
use Soa\EventSourcing\Repository\AggregateRoot;

class PayMoneyCommandHandler implements CommandHandler
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
     * @param PayMoneyCommand $command
     */
    public function handle(Command $command, AggregateRoot $state = null): EventStream
    {
        $transaction = $this->provider->payout($command->payee(), $command->amount(), $command->currencyCode(), $command->subjectId());
        if ($transaction->wasSucceed()) {
            return EventStream::fromDomainEvents(new MoneyPayed(
                    $command->aggregateRootId(),
                    $command->payee(),
                    $command->amount(),
                    $command->currencyCode(),
                    $command->subjectId(),
                    $this->provider->identifier(),
                    $transaction->id())
            );
        }

        return EventStream::fromDomainEvents(new PayMoneyFailed(
            $command->aggregateRootId(),
            $command->payee(),
            $command->amount(),
            $command->currencyCode(),
            $command->subjectId(),
            $this->provider->identifier(),
            $transaction->id(),
            $transaction->failureReason()
        ));
    }
}
