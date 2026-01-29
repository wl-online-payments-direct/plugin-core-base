<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring;

use DateTime;
use DateTimeInterface;
/**
 * Class MonitoringLog
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Monitoring
 */
class MonitoringLog
{
    private string $requestId;
    private string $orderId;
    private string $paymentNumber;
    /**
     * One of Critical, Error, Warning, Info and Debug.
     *
     * @var string
     */
    private string $logLevel;
    private string $message;
    private ?DateTime $createdAt;
    private string $requestMethod;
    private string $requestEndpoint;
    private string $requestBody;
    private string $statusCode;
    private string $responseBody;
    private string $transactionLink;
    private string $orderLink;
    /**
     * @param string $requestId
     * @param string $orderId
     * @param string $paymentNumber
     * @param string $logLevel
     * @param string $message
     * @param DateTime|null $createdAt
     * @param string $requestMethod
     * @param string $requestEndpoint
     * @param string $requestBody
     * @param string $statusCode
     * @param string $responseBody
     * @param string $transactionLink
     * @param string $orderLink
     */
    public function __construct(string $requestId, string $orderId, string $paymentNumber, string $logLevel, string $message, ?DateTime $createdAt, string $requestMethod, string $requestEndpoint, string $requestBody, string $statusCode, string $responseBody, string $transactionLink = '', string $orderLink = '')
    {
        $this->requestId = $requestId;
        $this->orderId = $orderId;
        $this->paymentNumber = $paymentNumber;
        $this->logLevel = $logLevel;
        $this->message = $message;
        $this->createdAt = $createdAt;
        $this->requestMethod = $requestMethod;
        $this->requestEndpoint = $requestEndpoint;
        $this->requestBody = $requestBody;
        $this->statusCode = $statusCode;
        $this->responseBody = $responseBody;
        $this->transactionLink = $transactionLink;
        $this->orderLink = $orderLink;
    }
    public function toArray(): array
    {
        return ['requestId' => $this->requestId, 'orderId' => $this->orderId, 'paymentNumber' => $this->paymentNumber, 'logLevel' => $this->logLevel, 'message' => $this->message, 'createdAt' => $this->createdAt->format(DateTimeInterface::ATOM), 'requestMethod' => $this->requestMethod, 'requestEndpoint' => $this->requestEndpoint, 'requestBody' => $this->requestBody, 'statusCode' => $this->statusCode, 'responseBody' => $this->responseBody, 'transactionLink' => $this->transactionLink, 'orderLink' => $this->orderLink];
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
    public function getLogLevel(): string
    {
        return $this->logLevel;
    }
    public function setLogLevel(string $logLevel): void
    {
        $this->logLevel = $logLevel;
    }
    public function getMessage(): string
    {
        return $this->message;
    }
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }
    public function setCreatedAt(?DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    public function getRequestMethod(): string
    {
        return $this->requestMethod;
    }
    public function setRequestMethod(string $requestMethod): void
    {
        $this->requestMethod = $requestMethod;
    }
    public function getRequestEndpoint(): string
    {
        return $this->requestEndpoint;
    }
    public function setRequestEndpoint(string $requestEndpoint): void
    {
        $this->requestEndpoint = $requestEndpoint;
    }
    public function getRequestBody(): string
    {
        return $this->requestBody;
    }
    public function setRequestBody(string $requestBody): void
    {
        $this->requestBody = $requestBody;
    }
    public function getStatusCode(): string
    {
        return $this->statusCode;
    }
    public function setStatusCode(string $statusCode): void
    {
        $this->statusCode = $statusCode;
    }
    public function getResponseBody(): string
    {
        return $this->responseBody;
    }
    public function setResponseBody(string $responseBody): void
    {
        $this->responseBody = $responseBody;
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
