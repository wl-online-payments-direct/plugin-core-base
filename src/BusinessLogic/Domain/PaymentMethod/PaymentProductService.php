<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod;

/**
 * Class PaymentProductService
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod
 */
class PaymentProductService
{
    public function getSupportedPaymentMethods(bool $withCards = \true): array
    {
        if ($withCards) {
            return PaymentProductId::SUPPORTED_PAYMENT_PRODUCTS;
        }
        return array_diff(PaymentProductId::SUPPORTED_PAYMENT_PRODUCTS, PaymentProductId::CARD_BRANDS);
    }
}
