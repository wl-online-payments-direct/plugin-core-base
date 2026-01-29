<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\CheckoutAPI;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Aspects\ErrorHandlingAspect;
use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Aspects\StoreContextAspect;
use WOP\OnlinePayments\Core\Bootstrap\Aspect\Aspects;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Controller\HostedCheckoutController;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Controller\HostedTokenizationController;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Controller\PaymentController;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Controller\PaymentMethodsController;
/**
 * Class CheckoutAPI. Integrations should use this class for communicating with Checkout API.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\CheckoutAPI
 */
class CheckoutAPI
{
    private function __construct()
    {
    }
    /**
     * @return CheckoutAPI
     */
    public static function get(): object
    {
        return Aspects::run(new ErrorHandlingAspect())->beforeEachMethodOfInstance(new CheckoutAPI());
    }
    /**
     * @param string $storeId
     *
     * @return PaymentMethodsController
     */
    public function paymentMethods(string $storeId): object
    {
        return Aspects::run(new ErrorHandlingAspect())->andRun(new StoreContextAspect($storeId))->beforeEachMethodOfService(PaymentMethodsController::class);
    }
    /**
     * @param string $storeId
     *
     * @return HostedTokenizationController
     */
    public function hostedTokenization(string $storeId): object
    {
        return Aspects::run(new ErrorHandlingAspect())->andRun(new StoreContextAspect($storeId))->beforeEachMethodOfService(HostedTokenizationController::class);
    }
    /**
     * @param string $storeId
     *
     * @return PaymentController
     */
    public function payment(string $storeId): object
    {
        return Aspects::run(new ErrorHandlingAspect())->andRun(new StoreContextAspect($storeId))->beforeEachMethodOfService(PaymentController::class);
    }
    /**
     * @param string $storeId
     *
     * @return HostedCheckoutController
     */
    public function hostedCheckout(string $storeId): object
    {
        return Aspects::run(new ErrorHandlingAspect())->andRun(new StoreContextAspect($storeId))->beforeEachMethodOfService(HostedCheckoutController::class);
    }
}
