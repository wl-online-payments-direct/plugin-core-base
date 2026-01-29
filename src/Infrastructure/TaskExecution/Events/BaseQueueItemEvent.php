<?php

namespace WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Events;

use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\QueueItem;
use WOP\OnlinePayments\Core\Infrastructure\Utility\Events\Event;
/**
 * Class BaseQueueItemEvent
 *
 * @package OnlinePayments\Core\Infrastructure\TaskExecution\Events
 */
abstract class BaseQueueItemEvent extends Event
{
    /**
     * @var QueueItem
     */
    protected QueueItem $queueItem;
    /**
     * BaseQueueItemEvent constructor.
     *
     * @param QueueItem $queueItem
     */
    public function __construct(QueueItem $queueItem)
    {
        $this->queueItem = $queueItem;
    }
    /**
     * @return QueueItem
     */
    public function getQueueItem(): QueueItem
    {
        return $this->queueItem;
    }
}
