<?php

namespace WOP\OnlinePayments\Core\Infrastructure\TaskExecution;

use WOP\OnlinePayments\Core\Infrastructure\Configuration\Configuration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use WOP\OnlinePayments\Core\Infrastructure\Serializer\Interfaces\Serializable;
use WOP\OnlinePayments\Core\Infrastructure\Serializer\Serializer;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Exceptions\AbortTaskExecutionException;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces\Priority;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\TaskEvents\AliveAnnouncedTaskEvent;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\TaskEvents\TaskProgressEvent;
use WOP\OnlinePayments\Core\Infrastructure\Utility\Events\EventEmitter;
use WOP\OnlinePayments\Core\Infrastructure\Utility\TimeProvider;
use DateTime;
use InvalidArgumentException;
use RuntimeException;
/**
 * Class Task
 * @package OnlinePayments\Core\Infrastructure\TaskExecution
 */
abstract class Task extends EventEmitter implements Serializable
{
    /**
     * Max inactivity period for a task in seconds
     */
    const MAX_INACTIVITY_PERIOD = 300;
    /**
     * Minimal number of seconds that must pass between two alive signals
     */
    const ALIVE_SIGNAL_FREQUENCY = 2;
    /**
     * Time of last invoked alive signal.
     *
     * @var ?DateTime
     */
    private ?DateTime $lastAliveSignalTime = null;
    /**
     * An instance of Configuration service.
     *
     * @var ?Configuration
     */
    private ?Configuration $configService = null;
    /**
     * Task execution id.
     *
     * @var ?string
     */
    private ?string $executionId = null;
    /**
     * Runs task logic.
     *
     * @throws AbortTaskExecutionException
     */
    abstract public function execute();
    /**
     * @inheritdoc
     */
    public function serialize(): string
    {
        return Serializer::serialize([]);
    }
    /**
     * @inheritdoc
     */
    public function unserialize(string $serialized): void
    {
        // This method was intentionally left blank because
        // this task doesn't have any properties which needs to encapsulate.
    }
    /**
     * @inheritDoc
     */
    public static function fromArray(array $array): Serializable
    {
        return new static();
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [];
    }
    /**
     * Retrieves task priority.
     *
     * @return int Task priority.
     */
    public function getPriority(): int
    {
        return Priority::NORMAL;
    }
    /**
     * Reports task progress by emitting @param float|int $progressPercent
     *   Float representation of progress percentage, value between 0 and 100 that will immediately
     *   be converted to base points. One base point is equal to 0.01%. For example 23.58% is
     *   equal to 2358 base points
     *
     * @throws InvalidArgumentException In case when progress percent is outside of 0 - 100 boundaries or not an float
     * @see TaskProgressEvent and defers next @see AliveAnnouncedTaskEvent.
     *
     */
    public function reportProgress($progressPercent)
    {
        if (!is_int($progressPercent) && !is_float($progressPercent)) {
            throw new InvalidArgumentException('Progress percentage must be value integer or float value');
        }
        /** @var TimeProvider $timeProvider */
        $timeProvider = ServiceRegister::getService(TimeProvider::CLASS_NAME);
        $this->lastAliveSignalTime = $timeProvider->getCurrentLocalTime();
        $this->fire(new TaskProgressEvent($this->percentToBasePoints($progressPercent)));
    }
    /**
     * Reports that task is alive by emitting
     *
     * @param boolean $force
     *
     * @see AliveAnnouncedTaskEvent.
     */
    public function reportAlive(bool $force = \false)
    {
        /** @var TimeProvider $timeProvider */
        $timeProvider = ServiceRegister::getService(TimeProvider::CLASS_NAME);
        $currentTime = $timeProvider->getCurrentLocalTime();
        if ($force || $this->lastAliveSignalTime === null || $this->lastAliveSignalTime->getTimestamp() + self::ALIVE_SIGNAL_FREQUENCY < $currentTime->getTimestamp()) {
            $this->fire(new AliveAnnouncedTaskEvent());
            $this->lastAliveSignalTime = $timeProvider->getCurrentLocalTime();
        }
    }
    /**
     * Gets max inactivity period for a task.
     *
     * @return int Max inactivity period for a task in seconds.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getMaxInactivityPeriod(): int
    {
        $configurationValue = $this->getConfigService()->getMaxTaskInactivityPeriod();
        return $configurationValue !== null ? $configurationValue : static::MAX_INACTIVITY_PERIOD;
    }
    /**
     * Gets name of the class.
     * Alias method for static method {@see self::getClassName()}
     *
     * @return string FQN of the task.
     */
    public function getType(): string
    {
        return static::getClassName();
    }
    /**
     * Gets name of the class.
     *
     * @return string FQN of the task.
     */
    public static function getClassName(): string
    {
        $namespaceParts = explode('\\', get_called_class());
        $name = end($namespaceParts);
        if ($name === 'Task') {
            throw new RuntimeException('Constant CLASS_NAME not defined in class ' . get_called_class());
        }
        return $name;
    }
    /**
     * Determines whether task can be reconfigured.
     *
     * @return bool TRUE if task can be reconfigured; otherwise, FALSE.
     */
    public function canBeReconfigured(): bool
    {
        return \false;
    }
    /**
     * Reconfigures the task.
     */
    public function reconfigure()
    {
    }
    /**
     * Gets execution Id.
     *
     * @return ?string Execution Id.
     */
    public function getExecutionId(): ?string
    {
        return $this->executionId;
    }
    /**
     * Sets Execution id.
     *
     * @param ?string $executionId Execution id.
     */
    public function setExecutionId(?string $executionId): void
    {
        $this->executionId = $executionId;
    }
    /**
     * Cleans up resources upon failure.
     */
    public function onFail()
    {
        // Extension stub.
    }
    /**
     * Cleans up resources upon abort.
     */
    public function onAbort()
    {
        // Extension stub.
    }
    /**
     * Calculates base points for progress tracking from percent value.
     *
     * @param float $percentValue Value in float representation.
     *
     * @return int Base points representation of percentage.
     */
    private function percentToBasePoints(float $percentValue): int
    {
        return (int) round($percentValue * 100, 2);
    }
    /**
     * Gets Configuration service.
     *
     * @return Configuration Service instance.
     */
    protected function getConfigService(): Configuration
    {
        if ($this->configService === null) {
            $this->configService = ServiceRegister::getService(Configuration::CLASS_NAME);
        }
        return $this->configService;
    }
}
