<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\MonitoringAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\MonitoringLog;
/**
 * Class MonitoringLogsResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\MonitoringAPI\Response
 */
class MonitoringLogsResponse extends Response
{
    /**
     * @var MonitoringLog[]
     */
    protected array $monitoringLogs;
    protected bool $nextPageAvailable;
    protected int $beginning;
    protected int $end;
    protected int $numberOfItems;
    /**
     * @param array $monitoringLogs
     * @param bool $nextPageAvailable
     * @param int $beginning
     * @param int $end
     * @param int $numberOfItems
     */
    public function __construct(array $monitoringLogs, bool $nextPageAvailable, int $beginning, int $end, int $numberOfItems)
    {
        $this->monitoringLogs = $monitoringLogs;
        $this->nextPageAvailable = $nextPageAvailable;
        $this->beginning = $beginning;
        $this->end = $end;
        $this->numberOfItems = $numberOfItems;
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->monitoringLogs as $monitoringLog) {
            $result[] = ['orderId' => $monitoringLog->getOrderId(), 'paymentNumber' => $monitoringLog->getPaymentNumber(), 'logLevel' => $monitoringLog->getLogLevel(), 'message' => $monitoringLog->getMessage(), 'createdAt' => $monitoringLog->getCreatedAt() ? $monitoringLog->getCreatedAt()->format('m-d-Y H:i:s') : '', 'requestMethod' => $monitoringLog->getRequestMethod(), 'requestEndpoint' => $monitoringLog->getRequestEndpoint(), 'requestBody' => $monitoringLog->getRequestBody(), 'statusCode' => (string) $monitoringLog->getStatusCode(), 'responseBody' => $monitoringLog->getResponseBody(), 'transactionLink' => $monitoringLog->getTransactionLink(), 'orderLink' => $monitoringLog->getOrderLink()];
        }
        return ['monitoringLogs' => $result, 'nextPageAvailable' => $this->nextPageAvailable, 'beginning' => $this->beginning, 'end' => $this->end, 'numberOfItems' => $this->numberOfItems];
    }
}
