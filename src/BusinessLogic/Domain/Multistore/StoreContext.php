<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore;

use Exception;
/**
 * Class StoreContext
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Multistore
 */
class StoreContext
{
    /**
     * @var ?StoreContext
     */
    private static ?StoreContext $instance = null;
    /**
     * @var string
     */
    private string $storeId = '';
    private string $origin = '';
    private function __construct()
    {
    }
    public static function getInstance(): StoreContext
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    /**
     * Executes callback method with set store id.
     *
     * @param string $storeId
     * @param callable $callback
     * @param array $params
     *
     * @throws Exception
     *
     * @return mixed
     */
    public static function doWithStore(string $storeId, callable $callback, array $params = [])
    {
        $previousStoreId = self::getInstance()->storeId;
        try {
            self::getInstance()->storeId = $storeId;
            $result = call_user_func_array($callback, $params);
        } finally {
            self::getInstance()->storeId = $previousStoreId;
        }
        return $result;
    }
    /**
     * Retrieves store id.
     *
     * @return string
     */
    public function getStoreId(): string
    {
        return $this->storeId;
    }
    public function getOrigin(): string
    {
        return $this->origin;
    }
    public function setOrigin(string $origin): void
    {
        // Only set origin if it has not been set before
        if (empty($this->origin)) {
            $this->origin = $origin;
        }
    }
    public function resetOrigin(): void
    {
        $this->origin = '';
    }
}
