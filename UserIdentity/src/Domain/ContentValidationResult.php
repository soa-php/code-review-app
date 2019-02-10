<?php

declare(strict_types=1);

namespace UserIdentity\Domain;

class ContentValidationResult
{
    /**
     * @var bool
     */
    private $wasSucceed;

    /**
     * @var string
     */
    private $failureReason;

    public static function succeed(): self
    {
        return new self(true, '');
    }

    public static function failed(string $reason): self
    {
        return new self(false, $reason);
    }

    private function __construct(bool $wasSucceed, string $failureReason)
    {
        $this->wasSucceed    = $wasSucceed;
        $this->failureReason = $failureReason;
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
