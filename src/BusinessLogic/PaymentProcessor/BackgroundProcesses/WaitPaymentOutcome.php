<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\BackgroundProcesses;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentTransaction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusCode;
/**
 * Class WaitPaymentOutcome.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\BackgroundProcesses
 */
class WaitPaymentOutcome
{
    private PaymentTransaction $paymentTransaction;
    private bool $isWaitingTimeExceeded;
    public function __construct(PaymentTransaction $paymentTransaction, bool $isWaitingTimeExceeded)
    {
        $this->paymentTransaction = $paymentTransaction;
        $this->isWaitingTimeExceeded = $isWaitingTimeExceeded;
    }
    public function isWaiting(): bool
    {
        return $this->getStatusCode()->isPending() && !$this->isWaitingTimeExceeded;
    }
    public function getStatusCode(): StatusCode
    {
        return $this->paymentTransaction->getStatusCode();
    }
    public function getPaymentTransaction(): PaymentTransaction
    {
        return $this->paymentTransaction;
    }
}
