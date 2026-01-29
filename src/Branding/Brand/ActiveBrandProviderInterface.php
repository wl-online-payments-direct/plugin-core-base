<?php

namespace WOP\OnlinePayments\Core\Branding\Brand;

/**
 * Interface ActiveBrandProviderInterface.
 *
 * @package OnlinePayments\Core\Branding\Brand
 */
interface ActiveBrandProviderInterface
{
    public function getActiveBrand(): BrandConfig;
    public function getApiUrl(): string;
    public function getTransactionUrl(): string;
}
