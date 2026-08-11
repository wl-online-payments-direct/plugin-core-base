<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\TenantContext;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\BackgroundProcesses\WaitPaymentOutcomeProcessStarterInterface;
use WOP\OnlinePayments\Core\Infrastructure\Logger\Logger;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces\AsyncProcessService;
/**
 * Interface WaitPaymentOutcomeProcessStarter.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses
 */
class WaitPaymentOutcomeProcessStarter implements WaitPaymentOutcomeProcessStarterInterface
{
    private AsyncProcessService $asyncProcessService;
    private StoreContext $storeContext;
    private TenantContext $tenantContext;
    public function __construct(AsyncProcessService $asyncProcessService, StoreContext $storeContext, TenantContext $tenantContext)
    {
        $this->asyncProcessService = $asyncProcessService;
        $this->storeContext = $storeContext;
        $this->tenantContext = $tenantContext;
    }
    public function startInBackground(?PaymentId $paymentId, ?string $returnHmac = null, ?string $merchantReference = null): void
    {
        try {
            $this->asyncProcessService->start(new WaitPaymentOutcomeProcessRunner($paymentId, $returnHmac, $merchantReference, $this->storeContext->getStoreId(), $this->tenantContext->getTenantId()));
        } catch (\Throwable $e) {
            Logger::logError('Unhandled error occurred during waiting payment outcome process starting in the background.', 'Core.WaitPaymentOutcomeProcessStarter', ['message' => $e->getMessage(), 'type' => get_class($e), 'trace' => $e->getTraceAsString()]);
        }
    }
}
