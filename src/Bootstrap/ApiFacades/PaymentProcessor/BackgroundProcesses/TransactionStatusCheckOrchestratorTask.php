<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses;

use WOP\OnlinePayments\Core\Bootstrap\DataAccess\PaymentTransaction\PendingTransactionsRepository;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\TenantContext;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\TenantRegistryInterface;
use WOP\OnlinePayments\Core\Infrastructure\Logger\Logger;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\QueueService;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Task;
/**
 * Class TransactionStatusCheckOrchestratorTask.
 *
 * Iterates all tenants and enqueues a TransactionStatusCheckTask for each tenant that has pending transactions.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses
 */
class TransactionStatusCheckOrchestratorTask extends Task
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
                    Logger::logWarning('Failed to enqueue transaction status check for tenant.', 'Core.TransactionStatusCheckOrchestratorTask', ['tenantId' => $tenantId, 'message' => $e->getMessage(), 'type' => get_class($e), 'trace' => $e->getTraceAsString()]);
                }
                $this->reportAlive();
            }
            $offset += $batchSize;
        }
        $this->reportProgress(100);
    }
    protected function enqueueForTenant(string $tenantId): void
    {
        if (empty($this->getPendingTransactionsRepository()->get(1))) {
            return;
        }
        $queueName = 'transaction_status_check' . ($tenantId !== '' ? '_' . $tenantId : '');
        $this->getQueueService()->enqueue($queueName, new TransactionStatusCheckTask($tenantId));
    }
    protected function getTenantRegistry(): TenantRegistryInterface
    {
        return ServiceRegister::getService(TenantRegistryInterface::class);
    }
    protected function getQueueService(): QueueService
    {
        return ServiceRegister::getService(QueueService::class);
    }
    protected function getPendingTransactionsRepository(): PendingTransactionsRepository
    {
        return ServiceRegister::getService(PendingTransactionsRepository::class);
    }
}
