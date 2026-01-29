<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\MonitoringAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
/**
 * Class DownloadWebhookLogsResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\MonitoringAPI\Response
 */
class DownloadWebhookLogsResponse extends Response
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
