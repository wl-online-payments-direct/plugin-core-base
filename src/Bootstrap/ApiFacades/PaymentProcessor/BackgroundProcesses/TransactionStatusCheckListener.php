<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\TenantRegistryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Time\TimeProviderInterface;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\BackgroundProcesses\FallbackCheckSchedule;
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
        // Gated by the schedule's finest-grained step - individual transactions are further filtered
        // against their own due time by FallbackCheckSchedule inside TransactionStatusCheckTask.
        $minutes = FallbackCheckSchedule::minimumIntervalInMinutes();
        $throttleCutoff = $this->timeProvider->getCurrentLocalTime()->sub(new \DateInterval("PT{$minutes}M"));
        return !$task || $task->getQueueTimestamp() < $throttleCutoff->getTimestamp();
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
