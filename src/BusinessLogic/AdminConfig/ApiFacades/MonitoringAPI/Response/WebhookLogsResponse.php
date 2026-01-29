<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\MonitoringAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\WebhookLog;
/**
 * Class WebhookLogsResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\MonitoringAPI\Response
 */
class WebhookLogsResponse extends Response
{
    /**
     * @var WebhookLog[]
     */
    protected array $webhookLogs;
    protected bool $nextPageAvailable;
    protected int $beginning;
    protected int $end;
    protected int $numberOfItems;
    /**
     * @param WebhookLog[] $webhookLogs
     */
    public function __construct(array $webhookLogs, bool $nextPageAvailable, int $beginning, int $end, int $numberOfItems)
    {
        $this->webhookLogs = $webhookLogs;
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
        foreach ($this->webhookLogs as $webhookLog) {
            $result[] = ['orderId' => $webhookLog->getOrderId(), 'paymentNumber' => $webhookLog->getPaymentNumber(), 'paymentMethod' => $webhookLog->getPaymentMethod(), 'status' => $webhookLog->getStatus(), 'type' => $webhookLog->getType(), 'createdAt' => $webhookLog->getCreatedAt() ? $webhookLog->getCreatedAt()->format('m-d-Y H:i:s') : '', 'statusCode' => (string) $webhookLog->getStatusCode(), 'webhookBody' => $webhookLog->getWebhookBody(), 'transactionLink' => $webhookLog->getTransactionLink(), 'orderLink' => $webhookLog->getOrderLink()];
        }
        return ['webhookLogs' => $result, 'nextPageAvailable' => $this->nextPageAvailable, 'beginning' => $this->beginning, 'end' => $this->end, 'numberOfItems' => $this->numberOfItems];
    }
}
