<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring;

use DateTime;
use DateTimeInterface;
/**
 * Class WebhookLog
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Monitoring
 */
class WebhookLog
{
    private string $orderId;
    private string $paymentNumber;
    private string $paymentMethod;
    private string $status;
    private string $type;
    private ?DateTime $createdAt;
    private int $statusCode;
    private string $webhookBody;
    private string $transactionLink;
    private string $orderLink;
    /**
     * @param string $orderId
     * @param string $paymentNumber
     * @param string $paymentMethod
     * @param string $status
     * @param string $type
     * @param DateTime|null $createdAt
     * @param int $statusCode
     * @param string $webhookBody
     * @param string $transactionLink
     * @param string $orderLink
     */
    public function __construct(string $orderId, string $paymentNumber, string $paymentMethod, string $status, string $type, ?DateTime $createdAt, int $statusCode, string $webhookBody, string $transactionLink = '', string $orderLink = '')
    {
        $this->orderId = $orderId;
        $this->paymentNumber = $paymentNumber;
        $this->paymentMethod = $paymentMethod;
        $this->status = $status;
        $this->type = $type;
        $this->createdAt = $createdAt;
        $this->statusCode = $statusCode;
        $this->webhookBody = $webhookBody;
        $this->transactionLink = $transactionLink;
        $this->orderLink = $orderLink;
    }
    public function toArray(): array
    {
        return ['orderId' => $this->orderId, 'paymentNumber' => $this->paymentNumber, 'paymentMethod' => $this->paymentMethod, 'status' => $this->status, 'type' => $this->type, 'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM), 'statusCode' => $this->statusCode, 'webhookBody' => $this->webhookBody, 'transactionLink' => $this->transactionLink, 'orderLink' => $this->orderLink];
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
    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    public function getWebhookBody(): string
    {
        return $this->webhookBody;
    }
    public function getTransactionLink(): string
    {
        return $this->transactionLink;
    }
    public function setTransactionLink(string $transactionLink): void
    {
        $this->transactionLink = $transactionLink;
    }
    public function getOrderLink(): string
    {
        return $this->orderLink;
    }
    public function setOrderLink(string $orderLink): void
    {
        $this->orderLink = $orderLink;
    }
}
