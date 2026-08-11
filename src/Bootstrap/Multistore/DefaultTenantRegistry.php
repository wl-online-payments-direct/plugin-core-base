<?php

namespace WOP\OnlinePayments\Core\Bootstrap\Multistore;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\TenantRegistryInterface;
/**
 * Class DefaultTenantRegistry
 *
 * @package OnlinePayments\Core\Bootstrap\Multistore
 */
class DefaultTenantRegistry implements TenantRegistryInterface
{
    /**
     * @inheritDoc
     */
    public function isMultiTenant(): bool
    {
        return \false;
    }
    /**
     * @inheritDoc
     */
    public function getTenantBatch(int $offset, int $batchSize): array
    {
        // Single-tenant on-premise: one "tenant" with empty ID
        return $offset === 0 ? [''] : [];
    }
}
