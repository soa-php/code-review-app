<?php

declare(strict_types=1);

namespace PullRequest\Domain\UseCase;

use Soa\EventSourcing\Command\ConventionBasedCommand;

class CreatePullRequestCommand extends ConventionBasedCommand
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $writer;

    public function __construct(string $code, string $writer)
    {
        $this->code   = $code;
        $this->writer = $writer;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function writer(): string
    {
        return $this->writer;
    }
}
