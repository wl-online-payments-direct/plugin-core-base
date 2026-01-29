<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
/**
 * Class Payment.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Payment
 */
class Payment
{
    private StatusCode $statusCode;
    private Amount $amount;
    private ?string $tokenId;
    private ?string $status;
    private ?string $productId;
    private ?string $paymentMethodName;
    public function __construct(StatusCode $statusCode, Amount $amount, ?string $tokenId, ?string $status = null, ?string $productId = null, ?string $paymentMethodName = null)
    {
        $this->statusCode = $statusCode;
        $this->amount = $amount;
        $this->tokenId = $tokenId;
        $this->status = $status;
        $this->productId = $productId;
        $this->paymentMethodName = $paymentMethodName;
    }
    public function getStatusCode(): StatusCode
    {
        return $this->statusCode;
    }
    public function getAmount(): Amount
    {
        return $this->amount;
    }
    public function getTokenId(): ?string
    {
        return $this->tokenId;
    }
    public function getStatus(): ?string
    {
        return $this->status;
    }
    public function getProductId(): ?string
    {
        return $this->productId;
    }
    public function getPaymentMethodName(): ?string
    {
        return $this->paymentMethodName;
    }
}
