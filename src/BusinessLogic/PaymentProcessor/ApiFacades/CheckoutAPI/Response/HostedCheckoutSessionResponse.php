<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\PaymentResponse;
/**
 * Class HostedCheckoutSessionResponse.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Response
 */
class HostedCheckoutSessionResponse extends Response
{
    private PaymentResponse $paymentResponse;
    public function __construct(PaymentResponse $paymentResponse)
    {
        $this->paymentResponse = $paymentResponse;
    }
    public function toArray(): array
    {
        return ['paymentTransaction' => ['paymentId' => (string) $this->paymentResponse->getPaymentTransaction()->getPaymentId(), 'statusCode' => $this->paymentResponse->getPaymentTransaction()->getStatusCode()->getCode(), 'returnHmac' => $this->paymentResponse->getPaymentTransaction()->getReturnHmac(), 'merchantReference' => $this->paymentResponse->getPaymentTransaction()->getMerchantReference()], 'redirectUrl' => $this->paymentResponse->getRedirectUrl()];
    }
    public function getRedirectUrl(): string
    {
        return (string) $this->paymentResponse->getRedirectUrl();
    }
    public function getReturnHmac(): string
    {
        return (string) $this->paymentResponse->getPaymentTransaction()->getReturnHmac();
    }
}
