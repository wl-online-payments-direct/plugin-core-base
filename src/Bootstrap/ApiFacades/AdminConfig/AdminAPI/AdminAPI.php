<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\AdminConfig\AdminAPI;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Aspects\ErrorHandlingAspect;
use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Aspects\StoreContextAspect;
use WOP\OnlinePayments\Core\Bootstrap\Aspect\Aspects;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ConnectionAPI\Controller\ConnectionController;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Controller\GeneralSettingsController;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\IntegrationAPI\Controller\IntegrationController;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\LanguageAPI\Controller\LanguageController;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\MonitoringAPI\Controller\LogsController;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Controller\PaymentController;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ProductTypesAPI\Controller\ProductTypesController;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\StoreAPI\Controller\StoreController;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\VersionsAPI\Controller\VersionController;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\AdminAPI\Controller\PaymentLinksController;
/**
 * Class AdminAPI. Integrations should use this class for communicating with Admin API.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\AdminConfig\AdminAPI
 */
class AdminAPI
{
    private function __construct()
    {
    }
    /**
     * @return AdminAPI
     */
    public static function get(): object
    {
        StoreContext::getInstance()->setOrigin('config');
        return Aspects::run(new ErrorHandlingAspect())->beforeEachMethodOfInstance(new AdminAPI());
    }
    public function connection(string $storeId): object
    {
        return Aspects::run(new ErrorHandlingAspect())->andRun(new StoreContextAspect($storeId))->beforeEachMethodOfService(ConnectionController::class);
    }
    public function version(): object
    {
        return Aspects::run(new ErrorHandlingAspect())->beforeEachMethodOfService(VersionController::class);
    }
    public function integration(string $storeId): object
    {
        return Aspects::run(new ErrorHandlingAspect())->andRun(new StoreContextAspect($storeId))->beforeEachMethodOfService(IntegrationController::class);
    }
    public function store(string $storeId): object
    {
        return Aspects::run(new ErrorHandlingAspect())->andRun(new StoreContextAspect($storeId))->beforeEachMethodOfService(StoreController::class);
    }
    public function payment(string $storeId): object
    {
        return Aspects::run(new ErrorHandlingAspect())->andRun(new StoreContextAspect($storeId))->beforeEachMethodOfService(PaymentController::class);
    }
    public function language(string $storeId): object
    {
        return Aspects::run(new ErrorHandlingAspect())->andRun(new StoreContextAspect($storeId))->beforeEachMethodOfService(LanguageController::class);
    }
    public function generalSettings(string $storeId): object
    {
        return Aspects::run(new ErrorHandlingAspect())->andRun(new StoreContextAspect($storeId))->beforeEachMethodOfService(GeneralSettingsController::class);
    }
    public function productTypes(): object
    {
        return Aspects::run(new ErrorHandlingAspect())->beforeEachMethodOfService(ProductTypesController::class);
    }
    public function monitoringLogs(string $storeId): object
    {
        return Aspects::run(new ErrorHandlingAspect())->andRun(new StoreContextAspect($storeId))->beforeEachMethodOfService(LogsController::class);
    }
    public function paymentLinks(string $storeId): object
    {
        return Aspects::run(new ErrorHandlingAspect())->andRun(new StoreContextAspect($storeId))->beforeEachMethodOfService(PaymentLinksController::class);
    }
}
