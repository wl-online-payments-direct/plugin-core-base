<?php

namespace WOP\OnlinePayments\Core\Infrastructure\TaskExecution\TaskEvents\Listeners;

use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\QueueItem;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\QueueService;
use RuntimeException;
/**
 * Class OnReportAlive
 *
 * @package OnlinePayments\Core\Infrastructure\TaskExecution\TaskEvents\Listeners
 */
class OnReportAlive
{
    /**
     * Handles report alive event.
     *
     * @param QueueItem $queueItem
     *
     * @throws QueueStorageUnavailableException
     */
    public static function handle(QueueItem $queueItem)
    {
        $queue = static::getQueue();
        $queue->keepAlive($queueItem);
        if ($queueItem->getParentId() === null) {
            return;
        }
        $parent = $queue->find($queueItem->getParentId());
        if ($parent === null) {
            throw new RuntimeException("Parent not available.");
        }
        $queue->keepAlive($parent);
    }
    /**
     * Provides queue service.
     *
     * @return QueueService
     */
    private static function getQueue(): QueueService
    {
        return ServiceRegister::getService(QueueService::CLASS_NAME);
    }
}
