<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ProductTypesAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\ProductTypes\ProductType;
/**
 * Class ProductTypesListResponse.
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ProductTypesAPI\Response
 */
class ProductTypesListResponse extends Response
{
    /**
     * @var ProductType[]
     */
    private array $availableProductTypes;
    private ?ProductType $selectedProductType;
    /**
     * @param ProductType[] $availableProductTypes
     * @param ?ProductType $selectedProductType
     */
    public function __construct(array $availableProductTypes, ?ProductType $selectedProductType)
    {
        $this->availableProductTypes = $availableProductTypes;
        $this->selectedProductType = $selectedProductType;
    }
    public function toArray(): array
    {
        return ['availableProductTypes' => array_map(function (ProductType $productType) {
            return (string) $productType;
        }, $this->availableProductTypes), 'selectedProductType' => (string) $this->selectedProductType];
    }
    /**
     * @return ProductType[]
     */
    public function getAvailableProductTypes(): array
    {
        return $this->availableProductTypes;
    }
    public function getSelectedProductType(): ?ProductType
    {
        return $this->selectedProductType;
    }
}
