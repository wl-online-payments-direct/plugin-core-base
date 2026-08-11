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
     * Payment product ids this brand must never offer, as plain id strings.
     *
     * This is the "during the build process, we can specify which payment methods are to be
     * available per brand" mechanism the V2 functional requirements ask for (p7): the brand config
     * file is itself a build artifact, so the exclusion list belongs beside the brand's endpoints
     * rather than in a second configuration channel. Optional, and empty for every brand that does
     * not declare it, so existing brand files keep working unchanged.
     *
     * @var string[]
     */
    private array $excludedPaymentProducts;
    /**
     * @param string $code
     * @param string $name
     * @param string $liveApiEndpoint
     * @param string $testApiEndpoint
     * @param string $liveUrl
     * @param string $testUrl
     * @param string $paymentMethodName
     * @param string[] $excludedPaymentProducts
     */
    public function __construct(string $code, string $name, string $liveApiEndpoint, string $testApiEndpoint, string $liveUrl, string $testUrl, string $paymentMethodName, array $excludedPaymentProducts = [])
    {
        $this->code = $code;
        $this->name = $name;
        $this->liveApiEndpoint = $liveApiEndpoint;
        $this->testApiEndpoint = $testApiEndpoint;
        $this->liveUrl = $liveUrl;
        $this->testUrl = $testUrl;
        $this->paymentMethodName = $paymentMethodName;
        // Normalised to strings: product ids are strings everywhere else in the domain
        // (PaymentProductId), but a hand-written brand JSON may well carry them as numbers.
        $this->excludedPaymentProducts = array_map('strval', array_values($excludedPaymentProducts));
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
    /**
     * @return string[]
     */
    public function getExcludedPaymentProducts(): array
    {
        return $this->excludedPaymentProducts;
    }
}
