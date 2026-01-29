<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\TaxableAmount;
/**
 * Class RoundingTotalsCartProvider.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart
 */
class RoundingTotalsCartProvider implements CartProvider
{
    private CartProvider $cartProvider;
    public function __construct(CartProvider $cartProvider)
    {
        $this->cartProvider = $cartProvider;
    }
    public function get(): Cart
    {
        $cart = $this->cartProvider->get();
        if ($cart->getLineItems()->isEmpty()) {
            return $cart;
        }
        $calculatedTotal = $cart->getLineItems()->getTotal();
        if (null !== $cart->getDiscount()) {
            $calculatedTotal = $calculatedTotal->minus($cart->getDiscount());
        }
        if (null !== $cart->getShipping()) {
            $calculatedTotal = $calculatedTotal->plus($cart->getShipping()->getCost()->getAmountInclTax());
        }
        $amountDifference = $cart->getTotal()->minus($calculatedTotal);
        if ($amountDifference->getValue() === 0) {
            return $cart;
        }
        if ($amountDifference->getValue() > 0) {
            $cart->getLineItems()->add(new LineItem(new Product('rounding', 'rounding'), TaxableAmount::fromAmountInclTax($amountDifference), 1));
        }
        if ($amountDifference->getValue() < 0) {
            $discount = $cart->getDiscount();
            $positiveDifference = $amountDifference->multiply(-1);
            $cart->setDiscount(null !== $discount ? $discount->plus($positiveDifference) : $positiveDifference);
        }
        return $cart;
    }
}
