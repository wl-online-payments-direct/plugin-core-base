<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\AdminAPI\Controller;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentLinks\PaymentLinkRequest;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\AdminAPI\Response\PaymentLinkResponse;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\PaymentLinks\PaymentLinksService;
/**
 * Class PaymentLinksController.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\AdminAPI\Controller
 */
class PaymentLinksController
{
    private PaymentLinksService $paymentLinksService;
    /**
     * @param PaymentLinksService $paymentLinksService
     */
    public function __construct(PaymentLinksService $paymentLinksService)
    {
        $this->paymentLinksService = $paymentLinksService;
    }
    public function create(PaymentLinkRequest $request): PaymentLinkResponse
    {
        return new PaymentLinkResponse($this->paymentLinksService->create($request));
    }
    public function get(string $merchantReference): PaymentLinkResponse
    {
        return new PaymentLinkResponse($this->paymentLinksService->get($merchantReference));
    }
}
