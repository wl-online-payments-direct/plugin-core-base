<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Metadata;

/**
 * Class Metadata.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Integration\Metadata
 */
class Metadata
{
    private string $platformName;
    private string $platformVersion;
    private string $platformVariant;
    private string $pluginVersion;
    private string $storeUrl;
    public function __construct(string $platformName = '', string $platformVersion = '', string $platformVariant = '', string $pluginVersion = '', string $storeUrl = '')
    {
        $this->platformName = $platformName;
        $this->platformVersion = $platformVersion;
        $this->platformVariant = $platformVariant;
        $this->pluginVersion = $pluginVersion;
        $this->storeUrl = $storeUrl;
    }
    public function getPlatformName(): string
    {
        return $this->platformName;
    }
    public function getPlatformVersion(): string
    {
        return $this->platformVersion;
    }
    public function getPlatformVariant(): string
    {
        return $this->platformVariant;
    }
    public function getPluginVersion(): string
    {
        return $this->pluginVersion;
    }
    public function getStoreUrl(): string
    {
        return $this->storeUrl;
    }
}
