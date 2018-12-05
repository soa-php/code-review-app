<?php

declare(strict_types=1);

namespace MergePullRequestPm\Domain;

use Soa\ProcessManager\Domain\State;

/**
 * @method static static EXCHANGING_MONEY
 * @method static static FINISHED
 * @method static static MERGING
 */
class MergePullRequestState extends State
{
    protected const EXCHANGING_MONEY = 'exchanging money';
    protected const MERGING          = 'merging';
    protected const FINISHED         = 'finished';
}
