<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\HostedCheckout;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Cart;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\CartProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\LineItem;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\LineItemCollection;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Product;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\TaxableAmount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\ProductTypes\ProductType;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Repositories\ProductTypeRepositoryInterface;
/**
 * Class MealvouchersCartProvider.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\HostedCheckout
 */
class MealvouchersCartProvider implements CartProvider
{
    private ProductTypeRepositoryInterface $productTypeRepository;
    private CartProvider $cartProvider;
    public function __construct(ProductTypeRepositoryInterface $productTypeRepository, CartProvider $cartProvider)
    {
        $this->productTypeRepository = $productTypeRepository;
        $this->cartProvider = $cartProvider;
    }
    public function get(): Cart
    {
        $cart = $this->cartProvider->get();
        $mealvouchersCart = new Cart($cart->getMerchantReference(), $cart->getTotal(), $cart->getTotalInEUR(), $cart->getCustomer(), $this->mergeLineItems($cart->getLineItems()), $cart->getShipping(), $cart->getDiscount());
        return $mealvouchersCart;
    }
    private function mergeLineItems(LineItemCollection $lineItems): LineItemCollection
    {
        $assignedProductTypes = $this->getMergedProductType();
        return new LineItemCollection([new LineItem(new Product($this->getMergedProductId($lineItems), $this->getMergedProductName($lineItems, $assignedProductTypes), $this->getMergedProductCode($lineItems), $assignedProductTypes, $this->getMergedProductUnit($lineItems)), $this->getMergedUnitPrice($lineItems), 1, $this->getMergedDiscounts($lineItems))]);
    }
    private function getMergedProductId(LineItemCollection $lineItems): string
    {
        if ($lineItems->getCount() === 1) {
            return $lineItems->toArray()[0]->getProduct()->getId();
        }
        return 'merged_item';
    }
    private function getMergedProductName(LineItemCollection $lineItems, ProductType $mergedProductType): string
    {
        $productNames = [];
        $totalQuantity = 0;
        foreach ($lineItems->toArray() as $lineItem) {
            if ($lineItem->getProduct()->getId() === 'rounding') {
                continue;
            }
            $totalQuantity += $lineItem->getQuantity();
            $productNames[] = $lineItem->getProduct()->getName();
        }
        $mergedName = join(' + ', $productNames);
        return strlen($mergedName) <= 50 ? $mergedName : substr("{$totalQuantity} {$mergedProductType} Items", 0, 50);
    }
    private function getMergedProductCode(LineItemCollection $lineItems): string
    {
        if ($lineItems->getCount() === 1) {
            return $lineItems->toArray()[0]->getProduct()->getUpcCode();
        }
        return 'Merged item';
    }
    private function getMergedUnitPrice(LineItemCollection $lineItems): TaxableAmount
    {
        return array_reduce($lineItems->toArray(), function (?TaxableAmount $total, LineItem $lineItem) {
            if (null === $total) {
                return $lineItem->getUnitPrice()->multiply($lineItem->getQuantity());
            }
            return $total->plus($lineItem->getUnitPrice()->multiply($lineItem->getQuantity()));
        });
    }
    private function getMergedProductUnit(LineItemCollection $lineItems): string
    {
        if ($lineItems->getCount() === 1) {
            return $lineItems->toArray()[0]->getProduct()->getUnit();
        }
        return 'Merged item';
    }
    private function getMergedDiscounts(LineItemCollection $lineItems): ?Amount
    {
        return array_reduce($lineItems->toArray(), function (?Amount $total, LineItem $lineItem) {
            if (null === $lineItem->getDiscount()) {
                return $total;
            }
            if (null === $total) {
                return $lineItem->getDiscount()->multiply($lineItem->getQuantity());
            }
            return $total->plus($lineItem->getDiscount()->multiply($lineItem->getQuantity()));
        });
    }
    private function getMergedProductType(): ProductType
    {
        $assignedProductTypes = $this->productTypeRepository->getProductTypesMap($this->cartProvider);
        $foodAndDrinkType = null;
        $homeAndGardenType = null;
        foreach ($assignedProductTypes as $productType) {
            if ($productType->equals(ProductType::foodAndDrink())) {
                $foodAndDrinkType = $productType;
            }
            if ($productType->equals(ProductType::homeAndGarden())) {
                $homeAndGardenType = $productType;
            }
        }
        if ($foodAndDrinkType) {
            return $foodAndDrinkType;
        }
        if ($homeAndGardenType) {
            return $homeAndGardenType;
        }
        return ProductType::giftAndFlowers();
    }
}
