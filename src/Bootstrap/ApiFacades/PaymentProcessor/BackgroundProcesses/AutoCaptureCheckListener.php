<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\TenantRegistryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Time\TimeProviderInterface;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\QueueService;
/**
 * Class AutoCaptureCheckListener.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses
 */
class AutoCaptureCheckListener
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
        $taskType = $this->tenantRegistry->isMultiTenant() ? AutoCaptureCheckOrchestratorTask::getClassName() : AutoCaptureCheckTask::getClassName();
        $task = $this->queueService->findLatestByType($taskType);
        $fifteenMinutesBeforeNow = $this->timeProvider->getCurrentLocalTime()->sub(new \DateInterval('PT15M'));
        return !$task || $task->getQueueTimestamp() < $fifteenMinutesBeforeNow->getTimestamp();
    }
    protected function doHandle(): void
    {
        if ($this->tenantRegistry->isMultiTenant()) {
            $this->queueService->enqueue('auto_capture_check_orchestrator', new AutoCaptureCheckOrchestratorTask());
            return;
        }
        $this->queueService->enqueue('auto_capture_check', new AutoCaptureCheckTask());
    }
}
