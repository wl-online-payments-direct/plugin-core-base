<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers\GetPaymentProductsParamsTransformer;
use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers\GetPaymentProductsResponseTransformer;
use WOP\OnlinePayments\Core\Bootstrap\Sdk\MerchantClientFactory;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Cart;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\ContextLogProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodCollection;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\PaymentMethodProxyInterface;
use OnlinePayments\Sdk\Merchant\Products\GetPaymentProductsParams;
/**
 * Class PaymentMethodProxy.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies
 */
class PaymentMethodProxy implements PaymentMethodProxyInterface
{
    private MerchantClientFactory $clientFactory;
    public function __construct(MerchantClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }
    public function getAvailablePaymentMethods(Cart $cart): PaymentMethodCollection
    {
        ContextLogProvider::getInstance()->setCurrentOrder($cart->getMerchantReference());
        return GetPaymentProductsResponseTransformer::transform($this->clientFactory->get()->products()->getPaymentProducts(GetPaymentProductsParamsTransformer::transform($cart)));
    }
    public function getSupportedPaymentMethods(string $countryCode, string $currencyCode): PaymentMethodCollection
    {
        $params = new GetPaymentProductsParams();
        $params->setCountryCode($countryCode);
        $params->setCurrencyCode($currencyCode);
        return GetPaymentProductsResponseTransformer::transform($this->clientFactory->get()->products()->getPaymentProducts($params));
    }
}
