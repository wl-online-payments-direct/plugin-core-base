<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Customer\Customer;
/**
 * Class Cart
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart
 */
class Cart
{
    private string $merchantReference;
    private Amount $total;
    private ?Amount $totalInEUR;
    private Customer $customer;
    private ?Shipping $shipping;
    private ?Amount $discount;
    private LineItemCollection $lineItems;
    /**
     * @param string $merchantReference
     * @param Amount $total
     * @param Amount|null $totalInEUR Set tota cart amount in currency EUR or null if EUR is not supported in shop
     * @param Customer $customer
     * @param LineItemCollection|null $lineItems
     * @param Shipping|null $shipping
     * @param Amount|null $discount
     */
    public function __construct(string $merchantReference, Amount $total, ?Amount $totalInEUR, Customer $customer, ?LineItemCollection $lineItems = null, ?Shipping $shipping = null, ?Amount $discount = null)
    {
        $this->merchantReference = $merchantReference;
        $this->total = $total;
        $this->totalInEUR = $totalInEUR;
        $this->customer = $customer;
        $this->lineItems = $lineItems ?? new LineItemCollection();
        $this->shipping = $shipping;
        $this->discount = $discount;
    }
    public function getMerchantReference(): string
    {
        return $this->merchantReference;
    }
    /**
     * @return Amount Total cart amount that should be paid in the end (with all taxes and discounts calculated)
     */
    public function getTotal(): Amount
    {
        return $this->total;
    }
    public function getTotalInEUR(): ?Amount
    {
        return $this->totalInEUR;
    }
    public function getCustomer(): Customer
    {
        return $this->customer;
    }
    public function getLineItems(): LineItemCollection
    {
        return $this->lineItems;
    }
    public function getShipping(): ?Shipping
    {
        return $this->shipping;
    }
    public function setShipping(Shipping $shipping): void
    {
        $this->shipping = $shipping;
    }
    public function getDiscount(): ?Amount
    {
        return $this->discount;
    }
    public function setDiscount(Amount $discount): void
    {
        $this->discount = $discount;
    }
}
