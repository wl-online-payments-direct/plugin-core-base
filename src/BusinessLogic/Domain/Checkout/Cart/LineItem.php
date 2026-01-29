<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\TaxableAmount;
/**
 * Class LineItem.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart
 */
class LineItem
{
    private Product $product;
    private TaxableAmount $unitPrice;
    private int $quantity;
    private ?Amount $discount;
    public function __construct(Product $product, TaxableAmount $unitPrice, int $quantity, Amount $discount = null)
    {
        $this->product = $product;
        $this->unitPrice = $unitPrice;
        $this->quantity = $quantity;
        $this->discount = $discount;
    }
    public function getTotal(): Amount
    {
        $total = $this->unitPrice->getAmountInclTax();
        if ($this->discount) {
            $total = $total->minus($this->discount);
        }
        return $total->multiply($this->quantity);
    }
    public function getProduct(): Product
    {
        return $this->product;
    }
    public function getUnitPrice(): TaxableAmount
    {
        return $this->unitPrice;
    }
    public function getQuantity(): int
    {
        return $this->quantity;
    }
    public function getDiscount(): ?Amount
    {
        return $this->discount;
    }
}
