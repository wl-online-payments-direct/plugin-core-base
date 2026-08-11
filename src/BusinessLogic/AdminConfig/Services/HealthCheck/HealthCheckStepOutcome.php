<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck;

/**
 * Class HealthCheckStepOutcome
 *
 * Outcome of a single health-check step: it passed, it failed with a known negative answer (bad
 * credentials, inactive account), it exhausted its retries on technical errors, or it was skipped
 * because an earlier step in the sequence did not pass.
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck
 */
class HealthCheckStepOutcome
{
    private const PASSED = 'passed';
    private const FAILED = 'failed';
    private const TECHNICAL_ERROR = 'technicalError';
    private const SKIPPED = 'skipped';
    private string $outcome;
    private function __construct(string $outcome)
    {
        $this->outcome = $outcome;
    }
    public static function passed(): self
    {
        return new self(self::PASSED);
    }
    public static function failed(): self
    {
        return new self(self::FAILED);
    }
    /**
     * The check could not be completed: every attempt, retries included, hit a network, timeout or
     * platform (5xx) failure. This says nothing about whether the credentials are valid.
     */
    public static function technicalError(): self
    {
        return new self(self::TECHNICAL_ERROR);
    }
    public static function skipped(): self
    {
        return new self(self::SKIPPED);
    }
    public function isPassed(): bool
    {
        return $this->outcome === self::PASSED;
    }
    public function isFailed(): bool
    {
        return $this->outcome === self::FAILED;
    }
    public function isTechnicalError(): bool
    {
        return $this->outcome === self::TECHNICAL_ERROR;
    }
    public function isSkipped(): bool
    {
        return $this->outcome === self::SKIPPED;
    }
    public function getName(): string
    {
        return $this->outcome;
    }
    public function __toString(): string
    {
        return $this->outcome;
    }
}
