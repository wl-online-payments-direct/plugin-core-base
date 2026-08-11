<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings;

/**
 * Class LogSettings
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings
 */
class LogSettings
{
    protected bool $webhookLogging;
    protected bool $requestResponseLogging;
    protected LogRecordsLifetime $logRecordsLifetime;
    /**
     * @param bool $webhookLogging Logs every received and sent webhook notification.
     * @param bool $requestResponseLogging Captures full raw API request/response payloads.
     * @param LogRecordsLifetime $logRecordsLifetime
     */
    public function __construct(bool $webhookLogging, bool $requestResponseLogging, LogRecordsLifetime $logRecordsLifetime)
    {
        $this->webhookLogging = $webhookLogging;
        $this->requestResponseLogging = $requestResponseLogging;
        $this->logRecordsLifetime = $logRecordsLifetime;
    }
    public function isWebhookLogging(): bool
    {
        return $this->webhookLogging;
    }
    public function isRequestResponseLogging(): bool
    {
        return $this->requestResponseLogging;
    }
    public function getLogRecordsLifetime(): LogRecordsLifetime
    {
        return $this->logRecordsLifetime;
    }
}
