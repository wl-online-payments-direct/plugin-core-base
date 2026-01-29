<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentLinks;

use DateTime;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
/**
 * Class PaymentLink.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentLinks
 */
class PaymentLink
{
    private ?string $paymentLinkId;
    private ?string $merchantReference;
    private ?PaymentId $paymentId;
    private ?DateTime $expiresAt;
    private ?string $redirectionUrl;
    private ?string $status;
    /**
     * @param string|null $paymentLinkId
     * @param string|null $merchantReference
     * @param PaymentId|null $paymentId
     * @param DateTime|null $expiresAt
     * @param string|null $redirectionUrl
     * @param string|null $status
     */
    public function __construct(?string $paymentLinkId, ?string $merchantReference, ?PaymentId $paymentId, ?DateTime $expiresAt, ?string $redirectionUrl, ?string $status)
    {
        $this->paymentLinkId = $paymentLinkId;
        $this->merchantReference = $merchantReference;
        $this->paymentId = $paymentId;
        $this->expiresAt = $expiresAt;
        $this->redirectionUrl = $redirectionUrl;
        $this->status = $status;
    }
    public function getPaymentLinkId(): ?string
    {
        return $this->paymentLinkId;
    }
    public function getPaymentId(): ?PaymentId
    {
        return $this->paymentId;
    }
    public function getExpiresAt(): ?DateTime
    {
        return $this->expiresAt;
    }
    public function getRedirectionUrl(): ?string
    {
        return $this->redirectionUrl;
    }
    public function getStatus(): ?string
    {
        return $this->status;
    }
    public function getMerchantReference(): ?string
    {
        return $this->merchantReference;
    }
}
