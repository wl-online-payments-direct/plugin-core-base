<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart;

/**
 * Class Product.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart
 */
class Product
{
    private string $id;
    private string $name;
    private string $upcCode;
    private string $type;
    private string $unit;
    public function __construct(string $id, string $name, string $upcCode = '', string $type = '', string $unit = 'piece')
    {
        $this->id = $id;
        $this->name = $name;
        $this->upcCode = $upcCode;
        $this->type = $type;
        $this->unit = $unit;
    }
    public function getId(): string
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getUpcCode(): string
    {
        return $this->upcCode;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function getUnit(): string
    {
        return $this->unit;
    }
}
