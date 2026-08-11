<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Request\Request;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidLogRecordsLifetimeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\LogRecordsLifetime;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\LogSettings;
/**
 * Class LogSettingsRequest
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request
 */
class LogSettingsRequest extends Request
{
    protected bool $webhookLogging;
    protected bool $requestResponseLogging;
    protected int $days;
    /**
     * @param bool $webhookLogging
     * @param bool $requestResponseLogging
     * @param int $days
     */
    public function __construct(bool $webhookLogging, bool $requestResponseLogging, int $days)
    {
        $this->webhookLogging = $webhookLogging;
        $this->requestResponseLogging = $requestResponseLogging;
        $this->days = $days;
    }
    /**
     * @inheritDoc
     *
     * @throws InvalidLogRecordsLifetimeException
     */
    public function transformToDomainModel(): object
    {
        return new LogSettings($this->webhookLogging, $this->requestResponseLogging, LogRecordsLifetime::create($this->days));
    }
}
