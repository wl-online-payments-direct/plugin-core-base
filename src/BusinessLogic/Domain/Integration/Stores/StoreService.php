<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Stores;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\OrderStatusMapping\Models\OrderStatusMapping;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Stores\Models\Store;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Stores\Models\StoreOrderStatus;
/**
 * Interface StoreService
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Integration\Stores
 */
interface StoreService
{
    /**
     * Returns shop domain/url.
     *
     * @return string
     */
    public function getStoreDomain(): string;
    /**
     * Returns all stores within a multiple environment.
     *
     * @return Store[]
     */
    public function getStores(): array;
    /**
     * Returns current active store.
     *
     * @return Store|null
     */
    public function getDefaultStore(): ?Store;
    /**
     * Returns Store object based on id given as first parameter.
     *
     * @param string $id
     *
     * @return Store|null
     */
    public function getStoreById(string $id): ?Store;
    /**
     * Returns default status mapping.
     *
     * @return OrderStatusMapping
     */
    public function getDefaultOrderStatusMapping(): OrderStatusMapping;
    /**
     * Returns array of StoreOrderStatus objects.
     *
     * @return StoreOrderStatus[]
     */
    public function getStoreOrderStatuses(): array;
}
