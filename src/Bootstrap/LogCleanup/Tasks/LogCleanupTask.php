<?php

namespace WOP\OnlinePayments\Core\Bootstrap\LogCleanup\Tasks;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\Repositories\MonitoringLogRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\Repositories\WebhookLogRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\TenantContext;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\TenantRegistryInterface;
use WOP\OnlinePayments\Core\Infrastructure\Logger\Logger;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Task;
/**
 * Class LogCleanupTask
 *
 * @package OnlinePayments\Core\Bootstrap\LogCleanup\Tasks
 */
class LogCleanupTask extends Task
{
    /**
     * @inheritDoc
     */
    public function execute(): void
    {
        $offset = 0;
        $batchSize = 100;
        while (($tenants = $this->getTenantRegistry()->getTenantBatch($offset, $batchSize)) !== []) {
            foreach ($tenants as $tenantId) {
                try {
                    TenantContext::doWithTenant($tenantId, function () {
                        $this->cleanupForTenant();
                    });
                } catch (\Throwable $e) {
                    Logger::logWarning('Failed to cleanup logs for tenant.', 'Core.LogCleanupTask', ['tenantId' => $tenantId, 'message' => $e->getMessage(), 'type' => get_class($e), 'trace' => $e->getTraceAsString()]);
                }
                $this->reportAlive();
            }
            $offset += $batchSize;
        }
        $this->reportProgress(100);
    }
    protected function cleanupForTenant(): void
    {
        $this->deleteLogs($this->getMonitoringLogRepository());
        $this->deleteLogs($this->getWebhookLogRepository());
    }
    /**
     * @param MonitoringLogRepositoryInterface | WebhookLogRepositoryInterface $repository
     *
     * @return void
     */
    protected function deleteLogs($repository): void
    {
        while ($repository->countExpired() > 0) {
            $repository->deleteExpired();
            $this->reportAlive();
        }
    }
    protected function getTenantRegistry(): TenantRegistryInterface
    {
        return ServiceRegister::getService(TenantRegistryInterface::class);
    }
    protected function getMonitoringLogRepository(): MonitoringLogRepositoryInterface
    {
        return ServiceRegister::getService(MonitoringLogRepositoryInterface::class);
    }
    protected function getWebhookLogRepository(): WebhookLogRepositoryInterface
    {
        return ServiceRegister::getService(WebhookLogRepositoryInterface::class);
    }
}
