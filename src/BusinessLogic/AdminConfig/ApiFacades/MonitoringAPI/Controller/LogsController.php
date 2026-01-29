<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\MonitoringAPI\Controller;

use Exception;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\MonitoringAPI\Response\DownloadMonitoringLogsResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\MonitoringAPI\Response\DownloadWebhookLogsResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\MonitoringAPI\Response\MonitoringLogsResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\MonitoringAPI\Response\WebhookLogsResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Monitoring\MonitoringLogsService;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Monitoring\WebhookLogsService;
/**
 * Class LogsController
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\MonitoringAPI\Controller
 */
class LogsController
{
    protected MonitoringLogsService $monitoringLogsService;
    protected WebhookLogsService $webhookLogsService;
    /**
     * @param MonitoringLogsService $monitoringLogsService
     * @param WebhookLogsService $webhookLogsService
     */
    public function __construct(MonitoringLogsService $monitoringLogsService, WebhookLogsService $webhookLogsService)
    {
        $this->monitoringLogsService = $monitoringLogsService;
        $this->webhookLogsService = $webhookLogsService;
    }
    /**
     * @param int $pageNumber
     * @param int $pageSize
     * @param string $searchTerm
     *
     * @return MonitoringLogsResponse
     *
     * @throws Exception
     */
    public function getMonitoringLogs(int $pageNumber = 1, int $pageSize = 10, string $searchTerm = ''): MonitoringLogsResponse
    {
        $numberOfItems = $this->monitoringLogsService->count($searchTerm);
        return new MonitoringLogsResponse($this->monitoringLogsService->getLogs($pageNumber, $pageSize, $searchTerm), $this->hasNextPage($pageNumber, $pageSize, $numberOfItems), ($pageNumber - 1) * $pageSize + 1, $pageNumber * $pageSize > $numberOfItems ? $numberOfItems : $pageNumber * $pageSize, $numberOfItems);
    }
    /**
     * @return DownloadMonitoringLogsResponse
     */
    public function downloadMonitoringLogs(): DownloadMonitoringLogsResponse
    {
        return new DownloadMonitoringLogsResponse($this->monitoringLogsService->getAllLogs());
    }
    /**
     * @param int $pageNumber
     * @param int $pageSize
     * @param string $searchTerm
     *
     * @return WebhookLogsResponse
     *
     * @throws Exception
     */
    public function getWebhookLogs(int $pageNumber = 1, int $pageSize = 10, string $searchTerm = ''): WebhookLogsResponse
    {
        $numberOfItems = $this->webhookLogsService->count($searchTerm);
        return new WebhookLogsResponse($this->webhookLogsService->getLogs($pageNumber, $pageSize, $searchTerm), $this->hasNextPage($pageNumber, $pageSize, $numberOfItems), ($pageNumber - 1) * $pageSize + 1, $pageNumber * $pageSize > $numberOfItems ? $numberOfItems : $pageNumber * $pageSize, $numberOfItems);
    }
    /**
     * @return DownloadWebhookLogsResponse
     */
    public function downloadWebhookLogs(): DownloadWebhookLogsResponse
    {
        return new DownloadWebhookLogsResponse($this->webhookLogsService->getAllLogs());
    }
    /**
     * @param int $pageNumber
     * @param int $pageSize
     * @param int $numberOfItems
     * @return bool
     */
    protected function hasNextPage(int $pageNumber, int $pageSize, int $numberOfItems): bool
    {
        if ($pageNumber <= 1) {
            return $pageSize < $numberOfItems;
        }
        return $pageNumber * $pageSize < $numberOfItems;
    }
}
