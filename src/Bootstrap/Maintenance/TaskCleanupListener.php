<?php

namespace WOP\OnlinePayments\Core\Bootstrap\Maintenance;

use DateInterval;
use DateTime;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces\Priority;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\QueueService;
/**
 * Class TaskCleanupListener
 *
 * @package OnlinePayments\Core\Bootstrap\Maintenance
 */
class TaskCleanupListener
{
    public function handle(): void
    {
        if (!$this->canHandle()) {
            return;
        }
        $this->doHandle();
    }
    /**
     * @return bool
     */
    protected function canHandle(): bool
    {
        $task = $this->getQueueService()->findLatestByType(TaskCleanupTask::getClassName());
        return !$task || $task->getQueueTimestamp() < (new DateTime())->sub(new DateInterval('P1D'))->getTimestamp();
    }
    protected function doHandle(): void
    {
        $this->getQueueService()->enqueue('task-cleanup', new TaskCleanupTask(), '', Priority::LOW);
    }
    protected function getQueueService(): QueueService
    {
        return ServiceRegister::getService(QueueService::class);
    }
}
