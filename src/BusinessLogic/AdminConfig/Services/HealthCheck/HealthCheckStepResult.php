<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck;

/**
 * Class HealthCheckStepResult
 *
 * Pairs a health-check step with its outcome.
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck
 */
class HealthCheckStepResult
{
    private HealthCheckStep $step;
    private HealthCheckStepOutcome $outcome;
    public function __construct(HealthCheckStep $step, HealthCheckStepOutcome $outcome)
    {
        $this->step = $step;
        $this->outcome = $outcome;
    }
    public function getStep(): HealthCheckStep
    {
        return $this->step;
    }
    public function getOutcome(): HealthCheckStepOutcome
    {
        return $this->outcome;
    }
}
