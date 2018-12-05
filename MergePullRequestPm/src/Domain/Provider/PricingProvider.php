<?php

declare(strict_types=1);

namespace MergePullRequestPm\Domain\Provider;

use Money\Money;

interface PricingProvider
{
    public function mergeFee(): Money;

    public function mergeReward(): Money;
}
