<?php

namespace WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Events;

use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\QueueItem;
use WOP\OnlinePayments\Core\Infrastructure\Utility\Events\Event;
/**
 * Class QueueStatusChangedEvent.
 *
 * @package OnlinePayments\Core\Infrastructure\Scheduler
 */
class QueueStatusChangedEvent extends Event
{
    /**
     * Fully qualified name of this class.
     */
    const CLASS_NAME = __CLASS__;
    /**
     * Queue item.
     *
     * @var QueueItem
     */
    private QueueItem $queueItem;
    /**
     * Previous state of queue item.
     *
     * @var string
     */
    private string $previousState;
    /**
     * TaskProgressEvent constructor.
     *
     * @param QueueItem $queueItem Queue item with changed status.
     * @param string $previousState Previous state. MUST be one of the states defined as constants in @see QueueItem.
     */
    public function __construct(QueueItem $queueItem, string $previousState)
    {
        $this->queueItem = $queueItem;
        $this->previousState = $previousState;
    }
    /**
     * Gets Queue item.
     *
     * @return QueueItem Queue item.
     */
    public function getQueueItem(): QueueItem
    {
        return $this->queueItem;
    }
    /**
     * Gets previous state.
     *
     * @return string Previous state.
     */
    public function getPreviousState(): string
    {
        return $this->previousState;
    }
}
