<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentLinks;

/**
 * Class PaymentLinkResponse.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentLinks
 */
class PaymentLinkResponse
{
    private ?PaymentLink $paymentLink;
    /**
     * @param PaymentLink|null $paymentLink
     */
    public function __construct(?PaymentLink $paymentLink)
    {
        $this->paymentLink = $paymentLink;
    }
    public function getPaymentLink(): ?PaymentLink
    {
        return $this->paymentLink;
    }
}
