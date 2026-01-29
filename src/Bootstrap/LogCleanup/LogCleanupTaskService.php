<?php

namespace WOP\OnlinePayments\Core\Bootstrap\LogCleanup;

use WOP\OnlinePayments\Core\Bootstrap\LogCleanup\Tasks\LogCleanupTask;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\LogCleanup\LogCleanupTaskServiceInterface;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\QueueService;
/**
 * Class LogCleanupTaskEnqueuer
 *
 * @package OnlinePayments\Core\Bootstrap\LogCleanup
 */
class LogCleanupTaskService implements LogCleanupTaskServiceInterface
{
    /**
     * @return int
     */
    public function findLatestExecutionTimestamp(): int
    {
        $task = $this->getQueueService()->findLatestByType(LogCleanupTask::getClassName());
        if (!$task) {
            return 0;
        }
        return $task->getQueueTimestamp();
    }
    /**
     * @return void
     *
     * @throws QueueStorageUnavailableException
     */
    public function enqueueLogCleanupTask(): void
    {
        $this->getQueueService()->enqueue('log-cleanup', new LogCleanupTask());
    }
    protected function getQueueService(): QueueService
    {
        return ServiceRegister::getService(QueueService::class);
    }
}
