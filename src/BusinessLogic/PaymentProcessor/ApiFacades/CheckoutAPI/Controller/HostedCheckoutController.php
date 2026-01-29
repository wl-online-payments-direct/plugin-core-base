<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Controller;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedCheckout\HostedCheckoutSessionRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Response\HostedCheckoutSessionResponse;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\HostedCheckout\HostedCheckoutService;
/**
 * Class HostedTokenizationController.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Controller
 */
class HostedCheckoutController
{
    private HostedCheckoutService $hostedCheckoutService;
    public function __construct(HostedCheckoutService $hostedCheckoutService)
    {
        $this->hostedCheckoutService = $hostedCheckoutService;
    }
    public function createSession(HostedCheckoutSessionRequest $request): HostedCheckoutSessionResponse
    {
        StoreContext::getInstance()->setOrigin($request->getPaymentProductId() ? 'checkoutPre' : 'checkoutHcp');
        return new HostedCheckoutSessionResponse($this->hostedCheckoutService->createSession($request));
    }
}
