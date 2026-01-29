<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentLinks\PaymentLink;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentLinks\PaymentLinkResponse;
use OnlinePayments\Sdk\Domain\PaymentLinkResponse as SdkPaymentLinkResponse;
/**
 * Class CreatePaymentLinkResponseTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers
 */
class CreatePaymentLinkResponseTransformer
{
    public static function transform(SdkPaymentLinkResponse $linkResponse): PaymentLinkResponse
    {
        return new PaymentLinkResponse(new PaymentLink($linkResponse->getPaymentLinkId(), $linkResponse->getPaymentLinkOrder()->getMerchantReference(), $linkResponse->getPaymentId() ? PaymentId::parse($linkResponse->getPaymentId()) : null, $linkResponse->getExpirationDate(), $linkResponse->getRedirectionUrl(), $linkResponse->getStatus()));
    }
}
