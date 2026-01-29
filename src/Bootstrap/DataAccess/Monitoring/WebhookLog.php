<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\Monitoring;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\WebhookLog as DomainWebhookLog;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
/**
 * Class WebhookLog
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\Monitoring
 */
class WebhookLog extends Entity
{
    public const CLASS_NAME = __CLASS__;
    protected string $storeId;
    protected string $mode;
    protected string $orderId;
    protected string $paymentNumber;
    protected int $createdAt;
    protected int $expiresAt;
    protected DomainWebhookLog $webhookLog;
    /**
     * @inheritDoc
     */
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('storeId');
        $indexMap->addStringIndex('mode');
        $indexMap->addStringIndex('orderId');
        $indexMap->addStringIndex('paymentNumber');
        $indexMap->addIntegerIndex('createdAt');
        $indexMap->addIntegerIndex('expiresAt');
        return new EntityConfiguration($indexMap, 'WebhookLog');
    }
    public function inflate(array $data): void
    {
        parent::inflate($data);
        $this->storeId = $data['storeId'];
        $this->mode = $data['mode'];
        $this->orderId = $data['orderId'];
        $this->paymentNumber = $data['paymentNumber'];
        $this->createdAt = $data['createdAt'];
        $this->expiresAt = $data['expiresAt'];
        $logData = $data['webhookLog'] ?? [];
        $this->webhookLog = new DomainWebhookLog($logData['orderId'] ?? '', $logData['paymentNumber'] ?? '', $logData['paymentMethod'] ?? '', $logData['status'] ?? '', $logData['type'] ?? '', $logData['createdAt'] ? \DateTime::createFromFormat('U', $logData['createdAt']) : null, $logData['statusCode'] ?? '', $logData['webhookBody'] ?? '', $logData['transactionLink'] ?? '', $logData['orderLink'] ?? '');
    }
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['storeId'] = $this->storeId;
        $data['mode'] = $this->mode;
        $data['orderId'] = $this->orderId;
        $data['paymentNumber'] = $this->paymentNumber;
        $data['createdAt'] = $this->createdAt;
        $data['expiresAt'] = $this->expiresAt;
        $data['webhookLog'] = ['orderId' => $this->webhookLog->getOrderId(), 'paymentNumber' => $this->webhookLog->getPaymentNumber(), 'paymentMethod' => $this->webhookLog->getPaymentMethod(), 'status' => $this->webhookLog->getStatus(), 'type' => $this->webhookLog->getType(), 'createdAt' => $this->webhookLog->getCreatedAt() ? $this->webhookLog->getCreatedAt()->getTimestamp() : '', 'statusCode' => $this->webhookLog->getStatusCode(), 'webhookBody' => $this->webhookLog->getWebhookBody(), 'transactionLink' => $this->webhookLog->getTransactionLink(), 'orderLink' => $this->webhookLog->getOrderLink()];
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
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }
    public function setCreatedAt(int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    public function getWebhookLog(): DomainWebhookLog
    {
        return $this->webhookLog;
    }
    public function setWebhookLog(DomainWebhookLog $webhookLog): void
    {
        $this->webhookLog = $webhookLog;
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
