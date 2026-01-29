<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\MonitoringAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
/**
 * Class DownloadMonitoringLogsResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\MonitoringAPI\Response
 */
class DownloadMonitoringLogsResponse extends Response
{
    /**
     * @var mixed[]
     */
    protected array $logs;
    /**
     * @param array $logs
     */
    public function __construct(array $logs)
    {
        $this->logs = $logs;
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->logs;
    }
}
