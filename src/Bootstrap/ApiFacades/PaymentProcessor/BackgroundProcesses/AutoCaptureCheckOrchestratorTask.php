<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses;

use WOP\OnlinePayments\Core\Bootstrap\DataAccess\PaymentTransaction\AuthorizedTransactionsRepository;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\TenantContext;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\TenantRegistryInterface;
use WOP\OnlinePayments\Core\Infrastructure\Logger\Logger;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\QueueService;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Task;
/**
 * Class AutoCaptureCheckOrchestratorTask.
 *
 * Iterates all tenants and enqueues an AutoCaptureCheckTask for each tenant that has authorized transactions.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses
 */
class AutoCaptureCheckOrchestratorTask extends Task
{
    public function execute(): void
    {
        $offset = 0;
        $batchSize = 100;
        while (($tenants = $this->getTenantRegistry()->getTenantBatch($offset, $batchSize)) !== []) {
            foreach ($tenants as $tenantId) {
                try {
                    TenantContext::doWithTenant($tenantId, function (string $tid) {
                        $this->enqueueForTenant($tid);
                    }, [$tenantId]);
                } catch (\Throwable $e) {
                    Logger::logWarning('Failed to enqueue auto capture check for tenant.', 'Core.AutoCaptureCheckOrchestratorTask', ['tenantId' => $tenantId, 'message' => $e->getMessage(), 'type' => get_class($e), 'trace' => $e->getTraceAsString()]);
                }
                $this->reportAlive();
            }
            $offset += $batchSize;
        }
        $this->reportProgress(100);
    }
    protected function enqueueForTenant(string $tenantId): void
    {
        if (empty($this->getAuthorizedTransactionsRepository()->get(1))) {
            return;
        }
        $queueName = 'auto_capture_check' . ($tenantId !== '' ? '_' . $tenantId : '');
        $this->getQueueService()->enqueue($queueName, new AutoCaptureCheckTask($tenantId));
    }
    protected function getTenantRegistry(): TenantRegistryInterface
    {
        return ServiceRegister::getService(TenantRegistryInterface::class);
    }
    protected function getQueueService(): QueueService
    {
        return ServiceRegister::getService(QueueService::class);
    }
    protected function getAuthorizedTransactionsRepository(): AuthorizedTransactionsRepository
    {
        return ServiceRegister::getService(AuthorizedTransactionsRepository::class);
    }
}
