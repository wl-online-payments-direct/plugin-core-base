<?php

namespace WOP\OnlinePayments\Core\Bootstrap\Maintenance;

use DateInterval;
use DateTime;
use WOP\OnlinePayments\Core\Bootstrap\DataAccess\Maintenance\TaskCleanupRepository;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces\Priority;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Task;
/**
 * Class TaskCleanupTask
 * In charge for deleting from the database tasks in specific statuses older than specific age (in seconds).
 *
 * @package OnlinePayments\Core\Bootstrap\Maintenance
 */
class TaskCleanupTask extends Task
{
    /**
     * @return int
     */
    public function getPriority(): int
    {
        return Priority::LOW;
    }
    public function execute(): void
    {
        $this->deleteCompletedTasks();
        $this->reportProgress(50);
        $this->deleteFailedTasks();
        $this->reportProgress(100);
    }
    protected function deleteCompletedTasks(): void
    {
        $repository = $this->getTaskCleanupRepository();
        $completedCount = $repository->getCompletedCount();
        $deletedCount = 0;
        $limit = 5000;
        while ($completedCount > $deletedCount) {
            $repository->deleteCompletedTasks($limit);
            $this->reportAlive();
            $deletedCount += $limit;
        }
    }
    protected function deleteFailedTasks(): void
    {
        $repository = $this->getTaskCleanupRepository();
        $failedCount = $repository->getFailedCount();
        $date = (new DateTime())->sub(new DateInterval('P14D'));
        $limit = 5000;
        $deletedCount = 0;
        while ($failedCount > $deletedCount) {
            $repository->deleteFailedTasks($date, $limit);
            $this->reportAlive();
            $deletedCount += $limit;
        }
    }
    private function getTaskCleanupRepository(): TaskCleanupRepository
    {
        return ServiceRegister::getService(TaskCleanupRepository::class);
    }
}
