<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment;

use DateTime;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentLinks\PaymentLink;
/**
 * Class PaymentTransaction.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentTransaction
 */
class PaymentTransaction
{
    private string $merchantReference;
    private ?PaymentId $paymentId;
    private ?string $returnHmac;
    private StatusCode $statusCode;
    private ?string $customerId;
    private ?DateTime $createdAt;
    private ?DateTime $updatedAt;
    /**
     * @var DateTime|null Time when customer is returned back to the shop system after a payment session
     */
    private ?DateTime $returnedAt;
    private ?string $paymentMethod;
    private ?DateTime $captureAt;
    private ?string $paymentLinkId;
    public function __construct(string $merchantReference, ?PaymentId $paymentId = null, ?string $returnHmac = null, ?StatusCode $statusCode = null, ?string $customerId = null, ?DateTime $createdAt = null, ?DateTime $updatedAt = null, ?DateTime $returnedAt = null, ?string $paymentMethod = null, ?DateTime $captureAt = null, ?string $paymentLinkId = null)
    {
        $this->merchantReference = $merchantReference;
        $this->paymentId = $paymentId;
        $this->returnHmac = $returnHmac;
        $this->statusCode = $statusCode ?? StatusCode::incomplete();
        $this->customerId = $customerId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->returnedAt = $returnedAt;
        $this->paymentMethod = $paymentMethod;
        $this->captureAt = $captureAt;
        $this->paymentLinkId = $paymentLinkId;
    }
    public function getMerchantReference(): string
    {
        return $this->merchantReference;
    }
    public function getPaymentId(): ?PaymentId
    {
        return $this->paymentId;
    }
    public function getReturnHmac(): ?string
    {
        return $this->returnHmac;
    }
    public function getStatusCode(): StatusCode
    {
        return $this->statusCode;
    }
    public function setStatusCode(StatusCode $statusCode): void
    {
        $this->statusCode = $statusCode;
    }
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }
    public function setCreatedAt(?DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(?DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
    public function getReturnedAt(): ?DateTime
    {
        return $this->returnedAt;
    }
    public function setReturnedAt(?DateTime $returnedAt): void
    {
        $this->returnedAt = $returnedAt;
    }
    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }
    public function setCustomerId(string $customerId): void
    {
        $this->customerId = $customerId;
    }
    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }
    public function setPaymentMethod(?string $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }
    public function getCaptureAt(): ?DateTime
    {
        return $this->captureAt;
    }
    public function setCaptureAt(?DateTime $captureAt): void
    {
        $this->captureAt = $captureAt;
    }
    public function getPaymentLinkId(): ?string
    {
        return $this->paymentLinkId;
    }
    public function setPaymentId(?PaymentId $paymentId): void
    {
        $this->paymentId = $paymentId;
    }
    public static function createFromPaymentLink(PaymentLink $paymentLink): PaymentTransaction
    {
        return new PaymentTransaction($paymentLink->getMerchantReference(), null, null, StatusCode::incomplete(), null, null, null, null, null, null, $paymentLink->getPaymentLinkId());
    }
}
