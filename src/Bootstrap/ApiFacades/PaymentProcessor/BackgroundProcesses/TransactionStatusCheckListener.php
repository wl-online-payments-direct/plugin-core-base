<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\TenantRegistryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Time\TimeProviderInterface;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\QueueService;
/**
 * Class TransactionStatusCheckListener.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses
 */
class TransactionStatusCheckListener
{
    private QueueService $queueService;
    private TimeProviderInterface $timeProvider;
    private TenantRegistryInterface $tenantRegistry;
    public function __construct(QueueService $queueService, TimeProviderInterface $timeProvider, TenantRegistryInterface $tenantRegistry)
    {
        $this->queueService = $queueService;
        $this->timeProvider = $timeProvider;
        $this->tenantRegistry = $tenantRegistry;
    }
    public function handle(): void
    {
        if (!$this->canHandle()) {
            return;
        }
        $this->doHandle();
    }
    protected function canHandle(): bool
    {
        $taskType = $this->tenantRegistry->isMultiTenant() ? TransactionStatusCheckOrchestratorTask::getClassName() : TransactionStatusCheckTask::getClassName();
        $task = $this->queueService->findLatestByType($taskType);
        $fifteenMinutesBeforeNow = $this->timeProvider->getCurrentLocalTime()->sub(new \DateInterval('PT15M'));
        return !$task || $task->getQueueTimestamp() < $fifteenMinutesBeforeNow->getTimestamp();
    }
    protected function doHandle(): void
    {
        if ($this->tenantRegistry->isMultiTenant()) {
            $this->queueService->enqueue('transaction_status_check_orchestrator', new TransactionStatusCheckOrchestratorTask());
            return;
        }
        $this->queueService->enqueue('transaction_status_check', new TransactionStatusCheckTask());
    }
}
