<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Cart;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodCollection;
/**
 * Interface PaymentMethodProxy.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies
 */
interface PaymentMethodProxyInterface
{
    /**
     * @param Cart $cart
     * @return PaymentMethodCollection
     */
    public function getAvailablePaymentMethods(Cart $cart): PaymentMethodCollection;
    /**
     * Same underlying "get payment products" call as getAvailablePaymentMethods(), but for a given
     * country/currency instead of a cart - used by the admin-config flow, which has neither.
     *
     * @param string $countryCode
     * @param string $currencyCode
     *
     * @return PaymentMethodCollection
     */
    public function getSupportedPaymentMethods(string $countryCode, string $currencyCode): PaymentMethodCollection;
}
