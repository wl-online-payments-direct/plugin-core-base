<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\ProductTypes;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\ProductTypes\Repositories\ProductTypeRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\CartProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\LineItem;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\ProductTypes\ProductType;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Repositories\ProductTypeRepositoryInterface as CheckoutAPIProductTypeRepositoryInterface;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Interfaces\ConditionallyDeletes;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\Operators;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\QueryFilter;
/**
 * Class ProductTypeRepository.
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\ProductTypes
 */
class ProductTypeRepository implements ProductTypeRepositoryInterface, CheckoutAPIProductTypeRepositoryInterface
{
    private ConditionallyDeletes $repository;
    public function __construct(ConditionallyDeletes $repository)
    {
        $this->repository = $repository;
    }
    public function getByProduct(string $productId): ?ProductType
    {
        $entity = $this->getProductTypeEntity($productId);
        return null !== $entity ? $entity->getProductType() : null;
    }
    public function assignTypeToProduct(string $productId, ProductType $productType): void
    {
        $entity = $this->getProductTypeEntity($productId);
        if ($entity) {
            $entity->setProductType($productType);
            $this->repository->update($entity);
            return;
        }
        $entity = new ProductTypeEntity();
        $entity->setProductId($productId);
        $entity->setProductType($productType);
        $this->repository->save($entity);
    }
    public function removeAssignmentFromProduct(string $productId): void
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('productId', Operators::EQUALS, $productId);
        $this->repository->deleteWhere($queryFilter);
    }
    public function getProductTypesMap(CartProvider $cartProvider): array
    {
        $lineItems = $cartProvider->get()->getLineItems();
        if ($lineItems->isEmpty()) {
            return [];
        }
        $productIds = array_map(function (LineItem $lineItem) {
            return $lineItem->getProduct()->getId();
        }, $lineItems->toArray());
        $productTypeMap = [];
        foreach ($this->getProductTypeEntitiesFor($productIds) as $productTypeEntity) {
            $productTypeMap[$productTypeEntity->getProductId()] = $productTypeEntity->getProductType();
        }
        return $productTypeMap;
    }
    private function getProductTypeEntity(string $productId): ?ProductTypeEntity
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('productId', Operators::EQUALS, $productId);
        /** @var ?ProductTypeEntity $entity */
        $entity = $this->repository->selectOne($queryFilter);
        return $entity;
    }
    /**
     * @param string[] $productIds
     * @return ProductTypeEntity[]
     */
    private function getProductTypeEntitiesFor(array $productIds): array
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('productId', Operators::IN, $productIds);
        /** @var ?ProductTypeEntity[] $entities */
        $entities = $this->repository->select($queryFilter);
        return $entities;
    }
}
