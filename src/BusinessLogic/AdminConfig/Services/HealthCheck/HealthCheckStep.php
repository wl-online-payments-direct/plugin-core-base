<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck;

/**
 * Class HealthCheckStep
 *
 * Identifies one of the three sequential health-check validations.
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck
 */
class HealthCheckStep
{
    private const TEST_CONNECTION = 'testconnection';
    private const VALIDATE_CREDENTIALS = 'validatecredentials';
    private const SEND_TEST = 'sendtest';
    private string $step;
    private function __construct(string $step)
    {
        $this->step = $step;
    }
    public static function testConnection(): self
    {
        return new self(self::TEST_CONNECTION);
    }
    public static function validateCredentials(): self
    {
        return new self(self::VALIDATE_CREDENTIALS);
    }
    public static function sendTest(): self
    {
        return new self(self::SEND_TEST);
    }
    public function equals(HealthCheckStep $other): bool
    {
        return $this->step === $other->step;
    }
    public function getName(): string
    {
        return $this->step;
    }
    public function __toString(): string
    {
        return $this->step;
    }
}
