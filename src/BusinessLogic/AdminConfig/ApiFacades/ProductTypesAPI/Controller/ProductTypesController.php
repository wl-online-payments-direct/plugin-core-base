<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ProductTypesAPI\Controller;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ProductTypesAPI\Response\ProductTypesListResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\ProductTypes\ProductTypeService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\ProductTypes\ProductType;
/**
 * Class ProductTypesController.
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ProductTypesAPI\Controller
 */
class ProductTypesController
{
    private ProductTypeService $productTypeService;
    public function __construct(ProductTypeService $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }
    public function list(string $productId): ProductTypesListResponse
    {
        return new ProductTypesListResponse([ProductType::foodAndDrink(), ProductType::homeAndGarden(), ProductType::giftAndFlowers()], $this->productTypeService->getForProduct($productId));
    }
    public function save(string $productId, ProductType $productType): void
    {
        $this->productTypeService->assignTypeToProduct($productId, $productType);
    }
    public function delete(string $productId): void
    {
        $this->productTypeService->removeAssignmentFromProduct($productId);
    }
}
