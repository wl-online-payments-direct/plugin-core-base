<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings;

/**
 * Class LogSettings
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings
 */
class LogSettings
{
    protected bool $debugMode;
    protected LogRecordsLifetime $logRecordsLifetime;
    /**
     * @param bool $debugMode
     * @param LogRecordsLifetime $logRecordsLifetime
     */
    public function __construct(bool $debugMode, LogRecordsLifetime $logRecordsLifetime)
    {
        $this->debugMode = $debugMode;
        $this->logRecordsLifetime = $logRecordsLifetime;
    }
    public function isDebugMode(): bool
    {
        return $this->debugMode;
    }
    public function getLogRecordsLifetime(): LogRecordsLifetime
    {
        return $this->logRecordsLifetime;
    }
}
