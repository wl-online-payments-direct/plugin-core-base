<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers\CreateHostedCheckoutRequestTransformer;
use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers\CreateHostedCheckoutResponseTransformer;
use WOP\OnlinePayments\Core\Bootstrap\Sdk\MerchantClientFactory;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedCheckout\HostedCheckoutSessionRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\PaymentResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Token;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\ContextLogProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodCollection;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\HostedCheckoutProxyInterface;
/**
 * Interface HostedCheckoutProxy.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies
 */
class HostedCheckoutProxy implements HostedCheckoutProxyInterface
{
    private MerchantClientFactory $clientFactory;
    public function __construct(MerchantClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }
    public function createSession(HostedCheckoutSessionRequest $request, ThreeDSSettings $cardsSettings, PaymentSettings $paymentSettings, PaymentMethodCollection $paymentMethodCollection, array $supportedPaymentMethods, ?Token $token = null): PaymentResponse
    {
        ContextLogProvider::getInstance()->setCurrentOrder($request->getCartProvider()->get()->getMerchantReference());
        return CreateHostedCheckoutResponseTransformer::transform($this->clientFactory->get()->hostedCheckout()->createHostedCheckout(CreateHostedCheckoutRequestTransformer::transform($request, $cardsSettings, $paymentSettings, $paymentMethodCollection, $supportedPaymentMethods, $token)));
    }
}
