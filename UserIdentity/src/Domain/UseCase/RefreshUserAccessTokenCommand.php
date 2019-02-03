<?php

declare(strict_types=1);

namespace UserIdentity\Domain\UseCase;

use Soa\EventSourcing\Command\ConventionBasedCommand;

class RefreshUserAccessTokenCommand extends ConventionBasedCommand
{
    /**
     * @var string
     */
    private $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
        $this->aggregateRootId = $userId;
    }

    public function userId(): string
    {
        return $this->userId;
    }
}
