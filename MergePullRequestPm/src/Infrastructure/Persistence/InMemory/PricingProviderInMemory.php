<?php

declare(strict_types=1);

namespace MergePullRequestPm\Infrastructure\Persistence\InMemory;

use MergePullRequestPm\Domain\Provider\PricingProvider;
use Money\Currency;
use Money\Money;

class PricingProviderInMemory implements PricingProvider
{
    public function mergeFee(): Money
    {
        return new Money(100, new Currency('EUR'));
    }

    public function mergeReward(): Money
    {
        return new Money(100, new Currency('EUR'));
    }
}
