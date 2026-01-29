<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentTransaction;
/**
 * Class PaymentResponse.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization
 */
class PaymentResponse
{
    private PaymentTransaction $paymentTransaction;
    private ?string $redirectUrl;
    public function __construct(PaymentTransaction $paymentTransaction, ?string $redirectUrl = null)
    {
        $this->paymentTransaction = $paymentTransaction;
        $this->redirectUrl = $redirectUrl;
    }
    public function getPaymentTransaction(): PaymentTransaction
    {
        return $this->paymentTransaction;
    }
    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }
}
