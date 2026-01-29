<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Stores\Models;

/**
 * Class Store
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Stores\Models
 */
class Store
{
    private string $storeId;
    private string $storeName;
    private bool $maintenanceMode;
    /**
     * @param string $storeId
     * @param string $storeName
     * @param bool $maintenanceMode
     */
    public function __construct(string $storeId, string $storeName, bool $maintenanceMode)
    {
        $this->storeId = $storeId;
        $this->storeName = $storeName;
        $this->maintenanceMode = $maintenanceMode;
    }
    /**
     * @return string
     */
    public function getStoreId(): string
    {
        return $this->storeId;
    }
    /**
     * @return string
     */
    public function getStoreName(): string
    {
        return $this->storeName;
    }
    /**
     * @return bool
     */
    public function isMaintenanceMode(): bool
    {
        return $this->maintenanceMode;
    }
}
