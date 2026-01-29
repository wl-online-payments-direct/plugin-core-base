<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentTransaction;
/**
 * Class PaymentTransactionResponse.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Response
 */
class PaymentTransactionResponse extends Response
{
    private ?PaymentTransaction $paymentTransaction;
    public function __construct(?PaymentTransaction $paymentTransaction)
    {
        $this->paymentTransaction = $paymentTransaction;
    }
    public function toArray(): array
    {
        if (null === $this->paymentTransaction) {
            return [];
        }
        return ['merchantReference' => $this->paymentTransaction->getMerchantReference(), 'paymentId' => (string) $this->paymentTransaction->getPaymentId(), 'returnHmac' => $this->paymentTransaction->getReturnHmac(), 'statusCode' => $this->paymentTransaction->getStatusCode()->getCode(), 'customerId' => $this->paymentTransaction->getCustomerId(), 'paymentMethod' => $this->paymentTransaction->getPaymentMethod()];
    }
    public function getPaymentTransaction(): ?PaymentTransaction
    {
        return $this->paymentTransaction;
    }
}
