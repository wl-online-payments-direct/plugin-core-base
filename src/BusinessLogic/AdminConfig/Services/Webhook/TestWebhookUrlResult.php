<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Webhook;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck\HealthCheckStepOutcome;
/**
 * Class TestWebhookUrlResult
 *
 * How the test webhook send went for one configured URL. The outcome vocabulary is the health check's
 * own (passed / failed / technicalError), because the send IS a health check step - see
 * HealthCheckService::sendTestWebhook().
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Webhook
 */
class TestWebhookUrlResult
{
    private string $url;
    private HealthCheckStepOutcome $outcome;
    public function __construct(string $url, HealthCheckStepOutcome $outcome)
    {
        $this->url = $url;
        $this->outcome = $outcome;
    }
    public function getUrl(): string
    {
        return $this->url;
    }
    public function getOutcome(): HealthCheckStepOutcome
    {
        return $this->outcome;
    }
}
