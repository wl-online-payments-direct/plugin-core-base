<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Controller;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\CartProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\MemoryCachingCartProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Response\PaymentMethodsResponse;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\HostedTokenization\HostedTokenizationService;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\PaymentMethod\PaymentMethodService;
/**
 * Class PaymentMethodsController.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Controller
 */
class PaymentMethodsController
{
    private PaymentMethodService $paymentMethodService;
    private HostedTokenizationService $hostedTokenizationService;
    public function __construct(PaymentMethodService $paymentMethodService, HostedTokenizationService $hostedTokenizationService)
    {
        $this->paymentMethodService = $paymentMethodService;
        $this->hostedTokenizationService = $hostedTokenizationService;
    }
    public function getAvailablePaymentMethods(CartProvider $cartProvider): PaymentMethodsResponse
    {
        StoreContext::getInstance()->setOrigin('checkoutLoad');
        $cartProvider = new MemoryCachingCartProvider($cartProvider);
        return new PaymentMethodsResponse($this->paymentMethodService->getAvailablePaymentMethods($cartProvider), $this->hostedTokenizationService->getValidTokens($cartProvider));
    }
}
