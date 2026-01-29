<?php

namespace WOP\OnlinePayments\Core\Infrastructure\TaskExecution;

use WOP\OnlinePayments\Core\Infrastructure\Logger\Logger;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
use WOP\OnlinePayments\Core\Infrastructure\Serializer\Serializer;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Exceptions\QueueItemDeserializationException;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces\Priority;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\TaskEvents\AliveAnnouncedTaskEvent;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\TaskEvents\Listeners\OnReportAlive;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\TaskEvents\Listeners\OnReportProgress;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\TaskEvents\TaskProgressEvent;
use WOP\OnlinePayments\Core\Infrastructure\Utility\TimeProvider;
use DateTime;
use InvalidArgumentException;
/**
 * Class QueueItem
 *
 * @package OnlinePayments\Core\Infrastructure\TaskExecution
 */
class QueueItem extends Entity
{
    /**
     * Fully qualified name of this class.
     */
    const CLASS_NAME = __CLASS__;
    /**
     * Indicates the "created" state of the queue item.
     */
    const CREATED = 'created';
    /**
     * Indicates the "queued" state of the queue item.
     */
    const QUEUED = 'queued';
    /**
     * Indicates the "in progress" state of the queue item.
     */
    const IN_PROGRESS = 'in_progress';
    /**
     * Indicates the "completed" state of the queue item.
     */
    const COMPLETED = 'completed';
    /**
     * Indicates the "failed" state of the queue item.
     */
    const FAILED = 'failed';
    /**
     * Indicates the "aborted" state of the queue item.
     */
    const ABORTED = 'aborted';
    /**
     * Array of simple field names.
     *
     * @var array
     */
    protected array $fields = ['id', 'parentId', 'status', 'context', 'serializedTask', 'queueName', 'lastExecutionProgressBasePoints', 'progressBasePoints', 'retries', 'failureDescription', 'createTime', 'startTime', 'finishTime', 'failTime', 'earliestStartTime', 'queueTime', 'lastUpdateTime', 'priority'];
    /**
     * Id of a parent orchestrator if queue item is a sub-job. NULL, otherwise.
     *
     * @var int | null
     */
    protected ?int $parentId = null;
    /**
     * Queue item status.
     *
     * @var string
     */
    protected string $status;
    /**
     * Task associated to queue item.
     *
     * @var ?Task
     */
    protected ?Task $task;
    /**
     * Context in which task will be executed.
     *
     * @var string
     */
    protected string $context;
    /**
     * String representation of task.
     *
     * @var ?string
     */
    protected ?string $serializedTask = null;
    /**
     * Integration queue name.
     *
     * @var ?string
     */
    protected ?string $queueName = null;
    /**
     * Last execution progress base points (integer value of 0.01%).
     *
     * @var int $lastExecutionProgressBasePoints
     */
    protected int $lastExecutionProgressBasePoints;
    /**
     * Current execution progress in base points (integer value of 0.01%).
     *
     * @var int $progressBasePoints
     */
    protected int $progressBasePoints;
    /**
     * Number of attempts to execute task.
     *
     * @var int
     */
    protected int $retries;
    /**
     * Description of failure when task fails.
     *
     * @var ?string
     */
    protected ?string $failureDescription;
    /**
     * Datetime when queue item is created.
     *
     * @var DateTime
     */
    protected $createTime;
    /**
     * Datetime when queue item is started.
     *
     * @var ?DateTime
     */
    protected $startTime = null;
    /**
     * Datetime when queue item is finished.
     *
     * @var ?DateTime
     */
    protected $finishTime;
    /**
     * Datetime when queue item is failed.
     *
     * @var ?DateTime
     */
    protected $failTime;
    /**
     * Min datetime when queue item can start.
     *
     * @var ?DateTime
     */
    protected $earliestStartTime = null;
    /**
     * Datetime when queue item is enqueued.
     *
     * @var ?DateTime
     */
    protected $queueTime = null;
    /**
     * Datetime when queue item is last updated.
     *
     * @var ?DateTime
     */
    protected $lastUpdateTime = null;
    /**
     * Specifies the execution priority of the queue item.
     *
     * @var ?int QueueItem execution priority.
     */
    protected ?int $priority = null;
    /**
     * Instance of time provider.
     *
     * @var TimeProvider
     */
    private $timeProvider;
    /**
     * QueueItem constructor.
     *
     * @param Task|null $task Associated task object.
     * @param string $context Context in which task will be executed.
     */
    public function __construct(Task $task = null, string $context = '')
    {
        $this->timeProvider = ServiceRegister::getService(TimeProvider::CLASS_NAME);
        $this->task = $task;
        $this->context = $context;
        $this->status = self::CREATED;
        $this->lastExecutionProgressBasePoints = 0;
        $this->progressBasePoints = 0;
        $this->retries = 0;
        $this->failureDescription = '';
        $this->createTime = $this->timeProvider->getCurrentLocalTime();
        $this->attachTaskEventHandlers();
    }
    /**
     * Sets queue item id.
     *
     * @param int $id Queue item id.
     */
    public function setId(int $id): void
    {
        parent::setId($id);
        if ($this->task !== null) {
            $this->task->setExecutionId($id);
        }
    }
    /**
     * Provides parent id.
     *
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }
    /**
     * Sets parent id.
     *
     * @param int|null $parentId
     */
    public function setParentId(?int $parentId)
    {
        $this->parentId = $parentId;
    }
    /**
     * Returns queueTime.
     *
     * @return ?DateTime queueTime Queue date and time.
     */
    public function getQueueTime(): ?DateTime
    {
        return $this->queueTime;
    }
    /**
     * Returns lastUpdateTime.
     *
     * @return DateTime lastUpdateTime Date and time of last update.
     */
    public function getLastUpdateTime(): DateTime
    {
        return $this->lastUpdateTime;
    }
    /**
     * Gets queue item status.
     *
     * @return string Queue item status.
     */
    public function getStatus(): string
    {
        return $this->status;
    }
    /**
     * Sets queue item status.
     *
     * @param string $status Queue item status.
     *  One of: QueueItem::CREATED, QueueItem::QUEUED, QueueItem::IN_PROGRESS, QueueItem::COMPLETED or QueueItem::FAILED
     */
    public function setStatus(string $status)
    {
        if (!in_array($status, [self::CREATED, self::QUEUED, self::IN_PROGRESS, self::COMPLETED, self::FAILED, self::ABORTED], \false)) {
            throw new InvalidArgumentException(sprintf('Invalid QueueItem status: "%s". ' . 'Status must be one of "%s", "%s", "%s", "%s", "%s" or "%s" values.', $status, self::CREATED, self::QUEUED, self::IN_PROGRESS, self::COMPLETED, self::FAILED, self::ABORTED));
        }
        $this->status = $status;
    }
    /**
     * Gets queue item queue name.
     *
     * @return ?string Queue item queue name.
     */
    public function getQueueName(): ?string
    {
        return $this->queueName;
    }
    /**
     * Sets queue item queue name.
     *
     * @param string $queueName Queue item queue name.
     */
    public function setQueueName(string $queueName)
    {
        $this->queueName = $queueName;
    }
    /**
     * Gets queue item last execution progress in base points as value between 0 and 10000.
     *
     * One base point is equal to 0.01%.
     * For example 23.58% is equal to 2358 base points.
     *
     * @return int Last execution progress expressed in base points.
     */
    public function getLastExecutionProgressBasePoints(): int
    {
        return $this->lastExecutionProgressBasePoints;
    }
    /**
     * Sets queue item last execution progress in base points, as value between 0 and 10000.
     *
     * One base point is equal to 0.01%.
     * For example 23.58% is equal to 2358 base points.
     *
     * @param int $lastExecutionProgressBasePoints Queue item last execution progress in base points.
     */
    public function setLastExecutionProgressBasePoints(int $lastExecutionProgressBasePoints)
    {
        if (!is_int($lastExecutionProgressBasePoints) || $lastExecutionProgressBasePoints < 0 || 10000 < $lastExecutionProgressBasePoints) {
            throw new InvalidArgumentException('Last execution progress percentage must be value between 0 and 100.');
        }
        $this->lastExecutionProgressBasePoints = $lastExecutionProgressBasePoints;
    }
    /**
     * Gets progress in percentage rounded to 2 decimal value.
     *
     * @return float QueueItem progress in percentage rounded to 2 decimal value.
     */
    public function getProgressFormatted(): float
    {
        return round($this->progressBasePoints / 100, 2);
    }
    /**
     * Gets queue item progress in base points as value between 0 and 10000.
     *
     * One base point is equal to 0.01%.
     * For example 23.58% is equal to 2358 base points.
     *
     * @return int Queue item progress percentage in base points.
     */
    public function getProgressBasePoints(): int
    {
        return $this->progressBasePoints;
    }
    /**
     * Sets queue item progress in base points, as value between 0 and 10000.
     *
     * One base point is equal to 0.01%.
     * For example 23.58% is equal to 2358 base points.
     *
     * @param mixed $progressBasePoints Queue item progress in base points.
     */
    public function setProgressBasePoints($progressBasePoints)
    {
        if (!is_int($progressBasePoints) || $progressBasePoints < 0 || 10000 < $progressBasePoints) {
            throw new InvalidArgumentException('Progress percentage must be value between 0 and 100.');
        }
        $this->progressBasePoints = $progressBasePoints;
    }
    /**
     * Gets queue item retries count.
     *
     * @return int Queue item retries count.
     */
    public function getRetries(): int
    {
        return $this->retries;
    }
    /**
     * Sets queue item retries count.
     *
     * @param int $retries Queue item retries count.
     */
    public function setRetries(int $retries)
    {
        $this->retries = $retries;
    }
    /**
     * Gets queue item task type.
     *
     * @return string Queue item task type.
     *
     */
    public function getTaskType(): string
    {
        try {
            return $this->getTask() ? $this->getTask()->getType() : '';
        } catch (\Exception $e) {
            return '';
        }
    }
    /**
     * Gets queue item associated task or null if not set.
     *
     * @return Task Task from queue item associated task.
     *
     * @throws QueueItemDeserializationException
     */
    public function getTask(): ?Task
    {
        if ($this->task === null) {
            try {
                $this->task = Serializer::unserialize($this->serializedTask);
            } catch (\Exception $e) {
                throw new QueueItemDeserializationException(json_encode(['Message' => $e->getMessage(), 'SerializedTask' => $this->serializedTask, 'QueueItemId' => $this->getId()]), 0, $e);
            }
            if (empty($this->task)) {
                throw new QueueItemDeserializationException(json_encode(['Message' => 'Unable to deserialize queue item task', 'SerializedTask' => $this->serializedTask, 'QueueItemId' => $this->getId()]));
            }
            $this->attachTaskEventHandlers();
        }
        return $this->task;
    }
    /**
     * Gets serialized queue item task.
     *
     * @return string
     *   Serialized representation of queue item task.
     */
    public function getSerializedTask(): string
    {
        if ($this->task === null) {
            return $this->serializedTask;
        }
        return Serializer::serialize($this->task);
    }
    /**
     * Sets serialized task representation.
     *
     * @param string $serializedTask Serialized representation of task.
     */
    public function setSerializedTask(string $serializedTask)
    {
        $this->serializedTask = $serializedTask;
        $this->task = null;
    }
    /**
     * Gets task execution context.
     *
     * @return string
     *   Context in which task will be executed.
     */
    public function getContext(): string
    {
        return $this->context;
    }
    /**
     * Sets task execution context. Context in which task will be executed.
     *
     * @param string $context Execution context.
     */
    public function setContext(string $context)
    {
        $this->context = $context;
    }
    /**
     * Gets queue item failure description.
     *
     * @return string
     *   Queue item failure description.
     */
    public function getFailureDescription(): string
    {
        return $this->failureDescription;
    }
    /**
     * Sets queue item failure description.
     *
     * @param ?string $failureDescription
     *   Queue item failure description.
     */
    public function setFailureDescription(?string $failureDescription)
    {
        $this->failureDescription = $failureDescription;
    }
    /**
     * Gets queue item created timestamp.
     *
     * @return int|null
     *   Queue item created timestamp.
     */
    public function getCreateTimestamp(): ?int
    {
        return $this->getTimestamp($this->createTime);
    }
    /**
     * Sets queue item created timestamp.
     *
     * @param int $timestamp
     *   Sets queue item created timestamp.
     */
    public function setCreateTimestamp(int $timestamp)
    {
        $this->createTime = $this->getDateTimeFromTimestamp($timestamp);
    }
    /**
     * Gets queue item start timestamp or null if task is not started.
     *
     * @return int|null
     *   Queue item start timestamp.
     */
    public function getStartTimestamp(): ?int
    {
        return $this->getTimestamp($this->startTime);
    }
    /**
     * Sets queue item start timestamp.
     *
     * @param ?int $timestamp
     *   Queue item start timestamp.
     */
    public function setStartTimestamp(?int $timestamp)
    {
        $this->startTime = $this->getDateTimeFromTimestamp($timestamp);
    }
    /**
     * Gets queue item finish timestamp or null if task is not finished.
     *
     * @return int|null
     *   Queue item finish timestamp.
     */
    public function getFinishTimestamp(): ?int
    {
        return $this->getTimestamp($this->finishTime);
    }
    /**
     * Sets queue item finish timestamp.
     *
     * @param ?int $timestamp Queue item finish timestamp.
     */
    public function setFinishTimestamp(?int $timestamp)
    {
        $this->finishTime = $this->getDateTimeFromTimestamp($timestamp);
    }
    /**
     * Gets queue item fail timestamp or null if task is not failed.
     *
     * @return int|null
     *   Queue item fail timestamp.
     */
    public function getFailTimestamp(): ?int
    {
        return $this->getTimestamp($this->failTime);
    }
    /**
     * Sets queue item fail timestamp.
     *
     * @param ?int $timestamp Queue item fail timestamp.
     */
    public function setFailTimestamp(?int $timestamp)
    {
        $this->failTime = $this->getDateTimeFromTimestamp($timestamp);
    }
    /**
     * Gets queue item earliest start timestamp or null if not set.
     *
     * @return int|null
     *   Queue item earliest start timestamp.
     */
    public function getEarliestStartTimestamp(): ?int
    {
        return $this->getTimestamp($this->earliestStartTime);
    }
    /**
     * Sets queue item earliest start timestamp.
     *
     * @param int $timestamp Queue item earliest start timestamp.
     */
    public function setEarliestStartTimestamp(int $timestamp)
    {
        $this->earliestStartTime = $this->getDateTimeFromTimestamp($timestamp);
    }
    /**
     * Gets queue item queue timestamp or null if task is not queued.
     *
     * @return int|null
     *   Queue item queue timestamp.
     */
    public function getQueueTimestamp(): ?int
    {
        return $this->getTimestamp($this->queueTime);
    }
    /**
     * Gets queue item queue timestamp.
     *
     * @param int $timestamp Queue item queue timestamp.
     */
    public function setQueueTimestamp(int $timestamp)
    {
        $this->queueTime = $this->getDateTimeFromTimestamp($timestamp);
    }
    /**
     * Gets queue item last updated timestamp or null if task was never updated.
     *
     * @return int|null
     *   Queue item last updated timestamp.
     */
    public function getLastUpdateTimestamp(): ?int
    {
        return $this->getTimestamp($this->lastUpdateTime);
    }
    /**
     * Sets queue item last updated timestamp.
     *
     * @param ?int $timestamp
     *   Queue item last updated timestamp.
     */
    public function setLastUpdateTimestamp(?int $timestamp)
    {
        $this->lastUpdateTime = $this->getDateTimeFromTimestamp($timestamp);
    }
    /**
     * Gets queue item last execution progress in base points as value between 0 and 10000.
     *
     * One base point is equal to 0.01%.
     * For example 23.58% is equal to 2358 base points.
     *
     * @return int Last execution progress expressed in base points.
     */
    public function getLastExecutionProgress(): int
    {
        return $this->lastExecutionProgressBasePoints;
    }
    /**
     * Retrieves queue item execution priority.
     *
     * @return int QueueItem execution priority.
     */
    public function getPriority(): int
    {
        return $this->priority ?: Priority::NORMAL;
    }
    /**
     * Sets queue item execution priority,.
     *
     * @param int $priority QueueItem execution priority.
     */
    public function setPriority(int $priority)
    {
        if (!in_array($priority, static::getAvailablePriorities(), \true)) {
            throw new InvalidArgumentException("Priority {$priority} is not supported.");
        }
        $this->priority = $priority;
    }
    /**
     * Reconfigures underlying task.
     *
     * @throws Exceptions\QueueItemDeserializationException
     */
    public function reconfigureTask()
    {
        $task = $this->getTask();
        if ($task && $task->canBeReconfigured()) {
            $task->reconfigure();
            $this->setRetries(0);
            Logger::logDebug('Task ' . $this->getTaskType() . ' reconfigured.');
        }
    }
    /**
     * Returns entity configuration object.
     *
     * @return EntityConfiguration Configuration object.
     */
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('status')->addStringIndex('taskType')->addStringIndex('queueName')->addStringIndex('context')->addDateTimeIndex('queueTime')->addIntegerIndex('lastExecutionProgress')->addIntegerIndex('lastUpdateTimestamp')->addIntegerIndex('priority')->addIntegerIndex('parentId');
        return new EntityConfiguration($indexMap, 'QueueItem');
    }
    /**
     * Transforms entity to its array format representation.
     *
     * @return array Entity in array format.
     */
    public function toArray(): array
    {
        $this->serializedTask = $this->getSerializedTask();
        $result = parent::toArray();
        $result['createTime'] = $this->timeProvider->serializeDate($this->createTime);
        $result['lastUpdateTime'] = $this->timeProvider->serializeDate($this->lastUpdateTime);
        $result['queueTime'] = $this->timeProvider->serializeDate($this->queueTime);
        $result['startTime'] = $this->timeProvider->serializeDate($this->startTime);
        $result['finishTime'] = $this->timeProvider->serializeDate($this->finishTime);
        $result['failTime'] = $this->timeProvider->serializeDate($this->failTime);
        $result['earliestStartTime'] = $this->timeProvider->serializeDate($this->earliestStartTime);
        $result['priority'] = $this->getPriority();
        return $result;
    }
    /**
     * Sets raw array data to this entity instance properties.
     *
     * @param array $data Raw array data with keys for class fields. @see self::$fields for field names.
     */
    public function inflate(array $data)
    {
        parent::inflate($data);
        $this->createTime = $this->timeProvider->deserializeDateString($data['createTime']);
        $this->lastUpdateTime = $this->timeProvider->deserializeDateString($data['lastUpdateTime']);
        $this->queueTime = $this->timeProvider->deserializeDateString($data['queueTime']);
        $this->startTime = $this->timeProvider->deserializeDateString($data['startTime']);
        $this->finishTime = $this->timeProvider->deserializeDateString($data['finishTime']);
        $this->failTime = $this->timeProvider->deserializeDateString($data['failTime']);
        $this->earliestStartTime = $this->timeProvider->deserializeDateString($data['earliestStartTime']);
    }
    /**
     * Defines available priorities.
     *
     * @return array
     */
    public static function getAvailablePriorities(): array
    {
        return [Priority::HIGH, Priority::NORMAL, Priority::LOW];
    }
    /**
     * Gets timestamp of datetime.
     *
     * @param DateTime|null $time Datetime object.
     *
     * @return int|null
     *   Timestamp of provided datetime or null if time is not defined.
     */
    protected function getTimestamp(DateTime $time = null): ?int
    {
        return $time !== null ? $time->getTimestamp() : null;
    }
    /**
     * Gets \DateTime object from timestamp.
     *
     * @param ?int $timestamp Timestamp in seconds.
     *
     * @return DateTime|null
     *  Object if successful; otherwise, null;
     */
    protected function getDateTimeFromTimestamp(?int $timestamp): ?DateTime
    {
        return !empty($timestamp) ? $this->timeProvider->getDateTime($timestamp) : null;
    }
    /**
     * Attach Task event handlers.
     */
    private function attachTaskEventHandlers()
    {
        if ($this->task === null) {
            return;
        }
        $self = $this;
        $this->task->setExecutionId($this->getId());
        $this->task->when(TaskProgressEvent::CLASS_NAME, static function (TaskProgressEvent $e) use ($self) {
            OnReportProgress::handle($self, $e->getProgressBasePoints());
        });
        $this->task->when(AliveAnnouncedTaskEvent::CLASS_NAME, static function () use ($self) {
            OnReportAlive::handle($self);
        });
    }
}
