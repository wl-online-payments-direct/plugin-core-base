<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses;

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
    public function __construct(QueueService $queueService, TimeProviderInterface $timeProvider)
    {
        $this->queueService = $queueService;
        $this->timeProvider = $timeProvider;
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
        $task = $this->queueService->findLatestByType(AutoCaptureCheckTask::getClassName());
        $fifteenMinutesBeforeNow = $this->timeProvider->getCurrentLocalTime()->sub(new \DateInterval('PT15M'));
        return !$task || $task->getQueueTimestamp() < $fifteenMinutesBeforeNow->getTimestamp();
    }
    protected function doHandle(): void
    {
        $this->queueService->enqueue('auto_capture_check', new AutoCaptureCheckTask());
    }
}
