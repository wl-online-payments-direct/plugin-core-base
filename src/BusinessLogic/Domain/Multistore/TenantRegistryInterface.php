<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore;

/**
 * Interface TenantRegistryInterface
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Multistore
 */
interface TenantRegistryInterface
{
    /**
     * Returns whether the system is running in multi-tenant mode.
     *
     * @return bool
     */
    public function isMultiTenant(): bool;
    /**
     * Returns a batch of tenant IDs starting from the given offset.
     * Returns empty array when no more tenants.
     *
     * @param int $offset Zero-based offset into the tenant list.
     * @param int $batchSize Maximum number of tenant IDs to return.
     *
     * @return string[]
     */
    public function getTenantBatch(int $offset, int $batchSize): array;
}
