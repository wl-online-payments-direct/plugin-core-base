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
}
