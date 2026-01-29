<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Cancel;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
/**
 * Class CancelRequest.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Cancel
 */
class CancelRequest
{
    private PaymentId $paymentId;
    private Amount $amount;
    private ?string $merchantReference;
    /**
     * @param PaymentId $paymentId
     * @param Amount $amount
     * @param string|null $merchantReference
     */
    public function __construct(PaymentId $paymentId, Amount $amount, ?string $merchantReference = null)
    {
        $this->paymentId = $paymentId;
        $this->amount = $amount;
        $this->merchantReference = $merchantReference;
    }
    public function getPaymentId(): PaymentId
    {
        return $this->paymentId;
    }
    public function getAmount(): Amount
    {
        return $this->amount;
    }
    public function getMerchantReference(): ?string
    {
        return $this->merchantReference;
    }
}
