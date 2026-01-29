<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\ProductTypes;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ProductTypes\Exceptions\InvalidProductTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class ProductType.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\ProductTypes
 */
class ProductType
{
    private const FOOD_AND_DRINK = 'FoodAndDrink';
    private const HOME_AND_GARDEN = 'HomeAndGarden';
    private const GIFT_AND_FLOWERS = 'GiftAndFlowers';
    private string $value;
    private function __construct(string $value)
    {
        $this->value = $value;
    }
    /**
     * String representation of the product type
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
    public static function parse(string $type): ProductType
    {
        if (!self::isSupported($type)) {
            throw new InvalidProductTypeException(new TranslatableLabel(sprintf('Product type is invalid %s.', $type), 'ProductType.invalidProductType', [$type]));
        }
        return new self($type);
    }
    public static function foodAndDrink(): ProductType
    {
        return new ProductType(self::FOOD_AND_DRINK);
    }
    public static function homeAndGarden(): ProductType
    {
        return new ProductType(self::HOME_AND_GARDEN);
    }
    public static function giftAndFlowers(): ProductType
    {
        return new ProductType(self::GIFT_AND_FLOWERS);
    }
    private static function isSupported(string $id): bool
    {
        return in_array($id, [self::FOOD_AND_DRINK, self::HOME_AND_GARDEN, self::GIFT_AND_FLOWERS], \true);
    }
    public function equals(ProductType $other): bool
    {
        return $this->value === $other->value;
    }
}
