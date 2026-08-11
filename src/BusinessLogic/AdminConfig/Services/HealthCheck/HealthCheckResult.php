<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck;

/**
 * Class HealthCheckResult
 *
 * Outcome of a full HealthCheckService::check() run - the per-step results, in sequence order.
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck
 */
class HealthCheckResult
{
    /**
     * @var HealthCheckStepResult[]
     */
    private array $steps;
    /**
     * @param HealthCheckStepResult[] $steps
     */
    public function __construct(array $steps)
    {
        $this->steps = $steps;
    }
    /**
     * @return HealthCheckStepResult[]
     */
    public function getSteps(): array
    {
        return $this->steps;
    }
    public function getStepResult(HealthCheckStep $step): ?HealthCheckStepResult
    {
        foreach ($this->steps as $stepResult) {
            if ($stepResult->getStep()->equals($step)) {
                return $stepResult;
            }
        }
        return null;
    }
    /**
     * Whether every step passed.
     */
    public function isSuccessful(): bool
    {
        foreach ($this->steps as $stepResult) {
            if (!$stepResult->getOutcome()->isPassed()) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * Whether the checked configuration is good enough to save: the Payment API test connection and
     * webhook credential validation both passed. Anything else on those two steps - a known negative, an
     * unrecoverable technical error, or a skip - blocks saving, because the credentials were never
     * confirmed. A mock webhook test that did not pass never blocks saving on its own; it only warrants a
     * warning to the merchant.
     */
    public function isSaveable(): bool
    {
        foreach ([HealthCheckStep::testConnection(), HealthCheckStep::validateCredentials()] as $gatingStep) {
            $stepResult = $this->getStepResult($gatingStep);
            if (!$stepResult || !$stepResult->getOutcome()->isPassed()) {
                return \false;
            }
        }
        return \true;
    }
}
