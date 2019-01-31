<?php

declare(strict_types=1);

namespace UserIdentity\Domain\UseCase;

use Soa\EventSourcing\Command\ConventionBasedCommand;

class RefreshUserAccessTokenCommand extends ConventionBasedCommand
{
    public function __construct(string $userId)
    {
        $this->aggregateRootId = $userId;
    }
}
