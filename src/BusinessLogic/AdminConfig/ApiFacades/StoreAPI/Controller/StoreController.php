<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\StoreAPI\Controller;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\StoreAPI\Response\StoreOrderStatusesResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\StoreAPI\Response\StoreResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\StoreAPI\Response\StoresResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Stores\Exceptions\FailedToRetrieveOrderStatusesException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Stores\Exceptions\FailedToRetrieveStoresException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Stores\Models\Store;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Stores\StoreService;
/**
 * Class StoreController
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\StoreAPI\Controller
 */
class StoreController
{
    private StoreService $storeService;
    /**
     * @param StoreService $storeService
     */
    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }
    /**
     * @return StoresResponse
     *
     * @throws FailedToRetrieveStoresException
     */
    public function getStores(): StoresResponse
    {
        return new StoresResponse($this->storeService->getStores());
    }
    /**
     * @return StoreResponse
     *
     * @throws FailedToRetrieveStoresException
     */
    public function getCurrentStore(): StoreResponse
    {
        $currentStore = $this->storeService->getCurrentStore();
        return $currentStore ? new StoreResponse($currentStore) : new StoreResponse($this->failbackStore());
    }
    /**
     * @return StoreOrderStatusesResponse
     *
     * @throws FailedToRetrieveOrderStatusesException
     */
    public function getStoreOrderStatuses(): StoreOrderStatusesResponse
    {
        return new StoreOrderStatusesResponse($this->storeService->getStoreOrderStatuses());
    }
    private function failBackStore(): Store
    {
        return new Store('failBack', 'failBack', \false);
    }
}
