<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\StoreAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Stores\Models\Store;
/**
 * Class StoreResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\StoreAPI\Response
 */
class StoreResponse extends Response
{
    private Store $store;
    /**
     * @param Store $store
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return ['storeId' => $this->store->getStoreId(), 'storeName' => $this->store->getStoreName(), 'maintenanceMode' => $this->store->isMaintenanceMode()];
    }
}
