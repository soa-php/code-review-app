<?php

declare(strict_types=1);

namespace Payment\Application\Projection;

use Payment\Domain\Event\CollectMoneyFailed;
use Payment\Domain\Event\MoneyCollected;
use Payment\Domain\Event\MoneyPayed;
use Payment\Domain\Event\PayMoneyFailed;
use Soa\EventSourcing\Projection\ConventionBasedProjector;

class PaymentProjector extends ConventionBasedProjector
{
    public function projectMoneyCollected(MoneyCollected $event, array $projection): array
    {
        $projection['id']                    = $event->streamId();
        $projection['payer']                 = $event->payer();
        $projection['amount']                = $event->amount();
        $projection['currencyCode']          = $event->currencyCode();
        $projection['subjectId']             = $event->subjectId();
        $projection['provider']              = $event->provider();
        $projection['providerTransactionId'] = $event->providerTransactionId();
        $projection['status']                = 'success';

        return $projection;
    }

    public function projectCollectMoneyFailed(CollectMoneyFailed $event, array $projection): array
    {
        $projection['id']                    = $event->streamId();
        $projection['payer']                 = $event->payer();
        $projection['amount']                = $event->amount();
        $projection['currencyCode']          = $event->currencyCode();
        $projection['subjectId']             = $event->subjectId();
        $projection['provider']              = $event->provider();
        $projection['providerTransactionId'] = $event->providerTransactionId();
        $projection['status']                = 'failure';
        $projection['reason']                = $event->reason();

        return $projection;
    }

    public function projectMoneyPayed(MoneyPayed $event, array $projection): array
    {
        $projection['id']                    = $event->streamId();
        $projection['payee']                 = $event->payee();
        $projection['amount']                = $event->amount();
        $projection['currencyCode']          = $event->currencyCode();
        $projection['subjectId']             = $event->subjectId();
        $projection['provider']              = $event->provider();
        $projection['providerTransactionId'] = $event->providerTransactionId();
        $projection['status']                = 'success';

        return $projection;
    }

    public function projectPayMoneyFailed(PayMoneyFailed $event, array $projection): array
    {
        $projection['id']                    = $event->streamId();
        $projection['payee']                 = $event->payee();
        $projection['amount']                = $event->amount();
        $projection['currencyCode']          = $event->currencyCode();
        $projection['subjectId']             = $event->subjectId();
        $projection['provider']              = $event->provider();
        $projection['providerTransactionId'] = $event->providerTransactionId();
        $projection['status']                = 'failure';
        $projection['reason']                = $event->reason();

        return $projection;
    }
}
