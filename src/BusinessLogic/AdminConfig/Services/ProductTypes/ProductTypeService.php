<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\ProductTypes;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\ProductTypes\Repositories\ProductTypeRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\ProductTypes\ProductType;
/**
 * Class ProductTypeService.
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\ProductTypes
 */
class ProductTypeService
{
    private ProductTypeRepositoryInterface $productTypeRepository;
    public function __construct(ProductTypeRepositoryInterface $productTypeRepository)
    {
        $this->productTypeRepository = $productTypeRepository;
    }
    public function getForProduct(string $productId): ?ProductType
    {
        return $this->productTypeRepository->getByProduct($productId);
    }
    public function assignTypeToProduct(string $productId, ProductType $productType): void
    {
        $this->productTypeRepository->assignTypeToProduct($productId, $productType);
    }
    public function removeAssignmentFromProduct(string $productId): void
    {
        $this->productTypeRepository->removeAssignmentFromProduct($productId);
    }
}
