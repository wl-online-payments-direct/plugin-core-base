<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\ProductTypes;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ProductTypes\ProductType;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
/**
 * Class ProductTypeEntity.
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\ProductTypes
 */
class ProductTypeEntity extends Entity
{
    public const CLASS_NAME = __CLASS__;
    private string $productId;
    private ProductType $productType;
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('productId');
        return new EntityConfiguration($indexMap, 'ProductTypeEntity');
    }
    public function inflate(array $data): void
    {
        parent::inflate($data);
        $this->productId = $data['productId'];
        $this->productType = ProductType::parse($data['productType']);
    }
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['productId'] = $this->productId;
        $data['productType'] = (string) $this->productType;
        return $data;
    }
    public function getProductId(): string
    {
        return $this->productId;
    }
    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }
    public function getProductType(): ProductType
    {
        return $this->productType;
    }
    public function setProductType(ProductType $productType): void
    {
        $this->productType = $productType;
    }
}
