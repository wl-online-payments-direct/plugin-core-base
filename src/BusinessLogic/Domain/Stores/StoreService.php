<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Stores;

use Exception;
use WOP\OnlinePayments\Core\Bootstrap\DataAccess\Connection\ConnectionConfigRepository;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Stores\StoreService as IntegrationStoreService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Stores\Exceptions\FailedToRetrieveOrderStatusesException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Stores\Exceptions\FailedToRetrieveStoresException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Stores\Models\Store;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Stores\Models\StoreOrderStatus;
/**
 * Class StoreService
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Stores
 */
class StoreService
{
    protected ConnectionConfigRepository $connectionRepository;
    protected IntegrationStoreService $integrationStoreService;
    public function __construct(IntegrationStoreService $integrationStoreService, ConnectionConfigRepository $connectionRepository)
    {
        $this->integrationStoreService = $integrationStoreService;
        $this->connectionRepository = $connectionRepository;
    }
    /**
     * @return Store[]
     *
     * @throws FailedToRetrieveStoresException
     */
    public function getStores(): array
    {
        try {
            return $this->integrationStoreService->getStores();
        } catch (Exception $e) {
            throw new FailedToRetrieveStoresException($e);
        }
    }
    /**
     * Returns first connected store. If it does not exist, default store is returned.
     *
     * @return Store|null
     * @throws FailedToRetrieveStoresException
     */
    public function getCurrentStore(): ?Store
    {
        try {
            $firstConnectedStoreId = $this->getFirstConnectedStoreId();
            return $firstConnectedStoreId ? $this->integrationStoreService->getStoreById($firstConnectedStoreId) : $this->integrationStoreService->getDefaultStore();
        } catch (Exception $e) {
            throw new FailedToRetrieveStoresException($e);
        }
    }
    /**
     * Returns ID of first store that was connected. If there is no store connected, empty string is returned.
     *
     * @return string
     */
    public function getFirstConnectedStoreId(): string
    {
        return $this->connectionRepository->getOldestConnectedStore();
    }
    /**
     * Returns array of StoreOrderStatus objects.
     *
     * @return StoreOrderStatus[]
     *
     * @throws FailedToRetrieveOrderStatusesException
     */
    public function getStoreOrderStatuses(): array
    {
        try {
            return $this->integrationStoreService->getStoreOrderStatuses();
        } catch (Exception $e) {
            throw new FailedToRetrieveOrderStatusesException($e);
        }
    }
}
