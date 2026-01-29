<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\AdminAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentLinks\PaymentLinkResponse as DomainPaymentLinkResponse;
/**
 * Class PaymentLinkResponse.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\AdminAPI\Response
 */
class PaymentLinkResponse extends Response
{
    private DomainPaymentLinkResponse $paymentLinkResponse;
    /**
     * @param DomainPaymentLinkResponse $paymentLinkResponse
     */
    public function __construct(DomainPaymentLinkResponse $paymentLinkResponse)
    {
        $this->paymentLinkResponse = $paymentLinkResponse;
    }
    public function toArray(): array
    {
        $paymentLink = $this->paymentLinkResponse->getPaymentLink();
        if (!$paymentLink) {
            return [];
        }
        return ['paymentLinkId' => $paymentLink->getPaymentLinkId(), 'paymentId' => $paymentLink->getPaymentId(), 'redirectionUrl' => $paymentLink->getRedirectionUrl(), 'expirationDate' => $paymentLink->getExpiresAt()->format('P')];
    }
    public function getRedirectUrl(): ?string
    {
        return $this->paymentLinkResponse->getPaymentLink() ? $this->paymentLinkResponse->getPaymentLink()->getRedirectionUrl() : null;
    }
}
