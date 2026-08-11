<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore;

/**
 * Class TenantContext
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Multistore
 */
class TenantContext
{
    /**
     * @var ?TenantContext
     */
    private static ?TenantContext $instance = null;
    /**
     * @var string
     */
    private string $tenantId = '';
    private function __construct()
    {
    }
    public static function getInstance(): TenantContext
    {
        if (!static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    /**
     * Executes callback with tenantId temporarily set, restores previous value after.
     *
     * @param string $tenantId
     * @param callable $callback
     * @param array $params
     *
     * @return mixed
     */
    public static function doWithTenant(string $tenantId, callable $callback, array $params = [])
    {
        $previousTenantId = self::getInstance()->tenantId;
        try {
            self::getInstance()->tenantId = $tenantId;
            $result = call_user_func_array($callback, $params);
        } finally {
            self::getInstance()->tenantId = $previousTenantId;
        }
        return $result;
    }
    /**
     * Retrieves tenant id.
     *
     * @return string
     */
    public function getTenantId(): string
    {
        return $this->tenantId;
    }
}
