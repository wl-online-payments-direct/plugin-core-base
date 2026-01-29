<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData;

/**
 * Class BankTransfer
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\RedirectPaymentMethod
 */
class BankTransfer implements PaymentMethodAdditionalData
{
    protected bool $instantPayment = \false;
    /**
     * @param bool $instantPayment
     */
    public function __construct(bool $instantPayment)
    {
        $this->instantPayment = $instantPayment;
    }
    /**
     * @return bool
     */
    public function isInstantPayment(): bool
    {
        return $this->instantPayment;
    }
}
