<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Exceptions\InvalidApiResponseException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Currency;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentCapture;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
use OnlinePayments\Sdk\Domain\Capture;
/**
 * Class PaymentCaptureResponseTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers
 */
class PaymentCaptureResponseTransformer
{
    public static function transform(Capture $capture): PaymentCapture
    {
        if (null === $capture->getCaptureOutput() || null === $capture->getStatusOutput() || null === $capture->getCaptureOutput()->getOperationReferences() || null === $capture->getCaptureOutput()->getOperationReferences()->getMerchantReference()) {
            throw new InvalidApiResponseException(new TranslatableLabel('Refund response is invalid. Refund status details missing in API response.', 'paymentProcessor.proxy.InvalidApiResponse'));
        }
        return new PaymentCapture(StatusCode::parse((int) $capture->getStatusOutput()->getStatusCode()), Amount::fromInt($capture->getCaptureOutput()->getAmountOfMoney()->getAmount(), Currency::fromIsoCode($capture->getCaptureOutput()->getAmountOfMoney()->getCurrencyCode())));
    }
}
