<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Exceptions\InvalidApiResponseException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Currency;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentRefund;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
use OnlinePayments\Sdk\Domain\RefundResponse;
/**
 * Class PaymentRefundResponseTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers
 */
class PaymentRefundResponseTransformer
{
    public static function transform(RefundResponse $refund): PaymentRefund
    {
        if (null === $refund->getRefundOutput() || null === $refund->getStatusOutput() || null === $refund->getRefundOutput()->getOperationReferences() || null === $refund->getRefundOutput()->getOperationReferences()->getMerchantReference()) {
            throw new InvalidApiResponseException(new TranslatableLabel('Refund response is invalid. Refund status details missing in API response.', 'paymentProcessor.proxy.InvalidApiResponse'));
        }
        return new PaymentRefund(StatusCode::parse((int) $refund->getStatusOutput()->getStatusCode()), Amount::fromInt($refund->getRefundOutput()->getAmountOfMoney()->getAmount(), Currency::fromIsoCode($refund->getRefundOutput()->getAmountOfMoney()->getCurrencyCode())));
    }
}
