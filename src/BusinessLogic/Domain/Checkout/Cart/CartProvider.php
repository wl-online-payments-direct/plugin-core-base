<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart;

/**
 * Class CartProvider
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart
 */
interface CartProvider
{
    public function get(): Cart;
}
