<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ConnectionAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck\HealthCheckResult;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck\HealthCheckStepResult;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
/**
 * Class ConnectionResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ConnectionAPI\Response
 */
class ConnectionResponse extends Response
{
    private HealthCheckResult $result;
    private bool $saved;
    /**
     * @param HealthCheckResult $result
     * @param bool $saved Whether the checked configuration was actually persisted. Always false for the
     *  check-only action, which validates a loaded configuration and writes nothing.
     */
    public function __construct(HealthCheckResult $result, bool $saved)
    {
        $this->result = $result;
        $this->saved = $saved;
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return ['saved' => $this->saved, 'steps' => array_map(static function (HealthCheckStepResult $stepResult): array {
            return ['step' => $stepResult->getStep()->getName(), 'outcome' => $stepResult->getOutcome()->getName()];
        }, $this->result->getSteps())];
    }
}
