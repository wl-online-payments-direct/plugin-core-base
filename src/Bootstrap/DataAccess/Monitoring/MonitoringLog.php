<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\Monitoring;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\MonitoringLog as DomainMonitoringLog;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
/**
 * Class MonitoringLog
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\Monitoring
 */
class MonitoringLog extends Entity
{
    public const CLASS_NAME = __CLASS__;
    protected string $storeId;
    protected string $mode;
    protected string $requestId;
    protected string $orderId;
    protected string $paymentNumber;
    protected string $message;
    protected int $createdAt;
    protected int $expiresAt;
    protected DomainMonitoringLog $monitoringLog;
    /**
     * @inheritDoc
     */
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('storeId');
        $indexMap->addStringIndex('mode');
        $indexMap->addStringIndex('requestId');
        $indexMap->addStringIndex('orderId');
        $indexMap->addStringIndex('paymentNumber');
        $indexMap->addStringIndex('message');
        $indexMap->addIntegerIndex('createdAt');
        $indexMap->addIntegerIndex('expiresAt');
        return new EntityConfiguration($indexMap, 'MonitoringLog');
    }
    /**
     * @throws \Exception
     */
    public function inflate(array $data): void
    {
        parent::inflate($data);
        $this->storeId = $data['storeId'];
        $this->mode = $data['mode'];
        $this->requestId = $data['requestId'] ?? '';
        $this->orderId = $data['orderId'];
        $this->paymentNumber = $data['paymentNumber'];
        $this->message = $data['message'];
        $this->createdAt = $data['createdAt'];
        $this->expiresAt = $data['expiresAt'];
        $logData = $data['monitoringLog'] ?? [];
        $this->monitoringLog = new DomainMonitoringLog($logData['requestId'] ?? '', $logData['orderId'] ?? '', $logData['paymentNumber'] ?? '', $logData['logLevel'] ?? '', $logData['message'] ?? '', $logData['createdAt'] ? \DateTime::createFromFormat('U', $logData['createdAt']) : null, $logData['requestMethod'] ?? '', $logData['requestEndpoint'] ?? '', $logData['requestBody'] ?? '', $logData['statusCode'] ?? '', $logData['responseBody'] ?? '', $logData['transactionLink'] ?? '', $logData['orderLink'] ?? '');
    }
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['storeId'] = $this->storeId;
        $data['mode'] = $this->mode;
        $data['requestId'] = $this->requestId;
        $data['orderId'] = $this->orderId;
        $data['paymentNumber'] = $this->paymentNumber;
        $data['message'] = $this->message;
        $data['createdAt'] = $this->createdAt;
        $data['expiresAt'] = $this->expiresAt;
        $data['monitoringLog'] = ['requestId' => $this->requestId, 'orderId' => $this->monitoringLog->getOrderId(), 'paymentNumber' => $this->monitoringLog->getPaymentNumber(), 'logLevel' => $this->monitoringLog->getLogLevel(), 'message' => $this->monitoringLog->getMessage(), 'createdAt' => $this->monitoringLog->getCreatedAt() ? $this->monitoringLog->getCreatedAt()->getTimestamp() : '', 'requestMethod' => $this->monitoringLog->getRequestMethod(), 'requestEndpoint' => $this->monitoringLog->getRequestEndpoint(), 'requestBody' => $this->monitoringLog->getRequestBody(), 'statusCode' => $this->monitoringLog->getStatusCode(), 'responseBody' => $this->monitoringLog->getResponseBody(), 'transactionLink' => $this->monitoringLog->getTransactionLink(), 'orderLink' => $this->monitoringLog->getOrderLink()];
        return $data;
    }
    public function getStoreId(): string
    {
        return $this->storeId;
    }
    public function setStoreId(string $storeId): void
    {
        $this->storeId = $storeId;
    }
    public function getMode(): string
    {
        return $this->mode;
    }
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }
    public function getRequestId(): string
    {
        return $this->requestId;
    }
    public function setRequestId(string $requestId): void
    {
        $this->requestId = $requestId;
    }
    public function getOrderId(): string
    {
        return $this->orderId;
    }
    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }
    public function getPaymentNumber(): string
    {
        return $this->paymentNumber;
    }
    public function setPaymentNumber(string $paymentNumber): void
    {
        $this->paymentNumber = $paymentNumber;
    }
    public function getMessage(): string
    {
        return $this->message;
    }
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }
    public function setCreatedAt(int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    public function getMonitoringLog(): DomainMonitoringLog
    {
        return $this->monitoringLog;
    }
    public function setMonitoringLog(DomainMonitoringLog $monitoringLog): void
    {
        $this->monitoringLog = $monitoringLog;
    }
    public function getExpiresAt(): int
    {
        return $this->expiresAt;
    }
    public function setExpiresAt(int $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }
}
