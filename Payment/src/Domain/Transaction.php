<?php

declare(strict_types=1);

namespace Payment\Domain;

class Transaction
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var bool
     */
    private $wasSucceed;

    /**
     * @var string
     */
    private $failureReason;

    public static function succeed(string $id): self
    {
        return new self($id, true, '');
    }

    public static function failed(string $id, $failureReason): self
    {
        return new self($id, false, $failureReason);
    }

    public function __construct(string $id, bool $wasSucceed, string $failureReason)
    {
        $this->id            = $id;
        $this->wasSucceed    = $wasSucceed;
        $this->failureReason = $failureReason;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function wasSucceed(): bool
    {
        return $this->wasSucceed;
    }

    public function failureReason(): string
    {
        return $this->failureReason;
    }
}
