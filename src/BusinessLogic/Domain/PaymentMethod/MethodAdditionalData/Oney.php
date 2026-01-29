<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData;

/**
 * Class Oney
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\RedirectPaymentMethod
 */
class Oney implements PaymentMethodAdditionalData
{
    protected string $paymentOption = '';
    /**
     * @param string $paymentOption
     */
    public function __construct(string $paymentOption)
    {
        $this->paymentOption = $paymentOption;
    }
    public function getPaymentOption(): string
    {
        return $this->paymentOption;
    }
}
