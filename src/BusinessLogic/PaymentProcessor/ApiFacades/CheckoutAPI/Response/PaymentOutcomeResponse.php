<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentTransaction;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\BackgroundProcesses\WaitPaymentOutcome;
/**
 * Class PaymentOutcomeResponse.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Response
 */
class PaymentOutcomeResponse extends Response
{
    private WaitPaymentOutcome $paymentOutcome;
    public function __construct(WaitPaymentOutcome $paymentOutcome)
    {
        $this->paymentOutcome = $paymentOutcome;
    }
    public function toArray(): array
    {
        return ['isWaiting' => $this->isWaiting()];
    }
    public function isWaiting(): bool
    {
        return $this->paymentOutcome->isWaiting();
    }
    public function getPaymentTransaction(): PaymentTransaction
    {
        return $this->paymentOutcome->getPaymentTransaction();
    }
}
