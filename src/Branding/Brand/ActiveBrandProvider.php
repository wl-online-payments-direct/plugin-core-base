<?php

namespace WOP\OnlinePayments\Core\Branding\Brand;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ActiveConnectionProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionMode;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
/**
 * Class ActiveBrandProvide.
 *
 * @package OnlinePayments\Core\Branding\Brand
 */
class ActiveBrandProvider implements ActiveBrandProviderInterface
{
    /**
     * @var callable
     */
    private $activeBrandResolver;
    private string $brandConfigFile;
    public function __construct(callable $activeBrandResolver, string $brandConfigFile)
    {
        $this->activeBrandResolver = $activeBrandResolver;
        $this->brandConfigFile = $brandConfigFile;
    }
    public function getActiveBrand(): BrandConfig
    {
        /** @var string $activeBrand */
        $activeBrand = call_user_func($this->activeBrandResolver);
        if (file_exists($this->brandConfigFile)) {
            $brandConfig = json_decode(file_get_contents($this->brandConfigFile), \true);
            return new BrandConfig($brandConfig['code'], $brandConfig['name'], $brandConfig['liveApiEndpoint'], $brandConfig['testApiEndpoint'], $brandConfig['liveUrl'], $brandConfig['testUrl'], $brandConfig['paymentMethodName']);
        }
        throw new \InvalidArgumentException("Brand ({$activeBrand}) configuration not found!");
    }
    public function getApiUrl(): string
    {
        /** @var ActiveConnectionProvider $activeConnectionProvider */
        $activeConnectionProvider = ServiceRegister::getService(ActiveConnectionProvider::class);
        return $activeConnectionProvider->get()->getMode()->equals(ConnectionMode::live()) ? $this->getActiveBrand()->getLiveUrl() : $this->getActiveBrand()->getTestUrl();
    }
    public function getTransactionUrl(): string
    {
        return $this->getApiUrl() . '/transactions/online/';
    }
}
