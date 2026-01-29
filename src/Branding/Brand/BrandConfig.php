<?php

namespace WOP\OnlinePayments\Core\Branding\Brand;

/**
 * Class BrandConfig.
 *
 * @package OnlinePayments\Core\Branding\Brand
 */
class BrandConfig
{
    private string $code;
    private string $name;
    private string $liveApiEndpoint;
    private string $testApiEndpoint;
    private string $liveUrl;
    private string $testUrl;
    private string $paymentMethodName;
    /**
     * @param string $code
     * @param string $name
     * @param string $liveApiEndpoint
     * @param string $testApiEndpoint
     * @param string $liveUrl
     * @param string $testUrl
     * @param string $paymentMethodName
     */
    public function __construct(string $code, string $name, string $liveApiEndpoint, string $testApiEndpoint, string $liveUrl, string $testUrl, string $paymentMethodName)
    {
        $this->code = $code;
        $this->name = $name;
        $this->liveApiEndpoint = $liveApiEndpoint;
        $this->testApiEndpoint = $testApiEndpoint;
        $this->liveUrl = $liveUrl;
        $this->testUrl = $testUrl;
        $this->paymentMethodName = $paymentMethodName;
    }
    public function getCode(): string
    {
        return $this->code;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getLiveApiEndpoint(): string
    {
        return $this->liveApiEndpoint;
    }
    public function getTestApiEndpoint(): string
    {
        return $this->testApiEndpoint;
    }
    public function getLiveUrl(): string
    {
        return $this->liveUrl;
    }
    public function getTestUrl(): string
    {
        return $this->testUrl;
    }
    public function getPaymentMethodName(): string
    {
        return $this->paymentMethodName;
    }
}
