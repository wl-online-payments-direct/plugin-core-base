<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers\CalculateSurchargeRequestTransformer;
use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers\CalculateSurchargeResponseTransformer;
use WOP\OnlinePayments\Core\Bootstrap\Sdk\MerchantClientFactory;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\SurchargeRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\SurchargeResponse;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\SurchargeProxyInterface;
/**
 * Class SurchargeProxy
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies
 */
class SurchargeProxy implements SurchargeProxyInterface
{
    private MerchantClientFactory $clientFactory;
    public function __construct(MerchantClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }
    /**
     * @inheritDoc
     */
    public function calculateSurcharge(SurchargeRequest $request): ?SurchargeResponse
    {
        return CalculateSurchargeResponseTransformer::transform($this->clientFactory->get()->services()->surchargeCalculation(CalculateSurchargeRequestTransformer::transform($request)));
    }
}
