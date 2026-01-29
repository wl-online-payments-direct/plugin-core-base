<?php

namespace WOP\OnlinePayments\Core\Infrastructure\TaskExecution;

use WOP\OnlinePayments\Core\Infrastructure\Logger\Logger;
use WOP\OnlinePayments\Core\Infrastructure\Serializer\Interfaces\Serializable;
use WOP\OnlinePayments\Core\Infrastructure\Serializer\Serializer;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Exceptions\TaskRunnerRunException;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Exceptions\TaskRunnerStatusStorageUnavailableException;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces\Runnable;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces\TaskRunnerStatusStorage;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces\TaskRunnerWakeup;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\TaskEvents\TickEvent;
use WOP\OnlinePayments\Core\Infrastructure\Utility\Events\EventBus;
use Exception;
/**
 * Class TaskRunnerStarter.
 *
 * @package OnlinePayments\Core\Infrastructure\TaskExecution
 */
class TaskRunnerStarter implements Runnable
{
    /**
     * Unique runner guid.
     *
     * @var string
     */
    private string $guid;
    /**
     * Instance of task runner status storage.
     *
     * @var ?TaskRunnerStatusStorage
     */
    private ?TaskRunnerStatusStorage $runnerStatusStorage = null;
    /**
     * Instance of task runner.
     *
     * @var ?TaskRunner
     */
    private ?TaskRunner $taskRunner = null;
    /**
     * Instance of task runner wakeup service.
     *
     * @var ?TaskRunnerWakeup
     */
    private ?TaskRunnerWakeup $taskWakeup = null;
    /**
     * TaskRunnerStarter constructor.
     *
     * @param string $guid Unique runner guid.
     */
    public function __construct(string $guid)
    {
        $this->guid = $guid;
    }
    /**
     * Transforms array into an serializable object,
     *
     * @param array $array Data that is used to instantiate serializable object.
     *
     * @return Serializable
     *      Instance of serialized object.
     */
    public static function fromArray(array $array): Serializable
    {
        return new static($array['guid']);
    }
    /**
     * Transforms serializable object into an array.
     *
     * @return array Array representation of a serializable object.
     */
    public function toArray(): array
    {
        return ['guid' => $this->guid];
    }
    /**
     * String representation of object.
     *
     * @inheritdoc
     */
    public function serialize(): string
    {
        return Serializer::serialize([$this->guid]);
    }
    /**
     * Constructs the object.
     *
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        list($this->guid) = Serializer::unserialize($serialized);
    }
    /**
     * Get unique runner guid.
     *
     * @return string Unique runner string.
     */
    public function getGuid(): string
    {
        return $this->guid;
    }
    /**
     * Starts synchronously currently active task runner instance.
     */
    public function run()
    {
        try {
            $this->doRun();
        } catch (TaskRunnerStatusStorageUnavailableException $ex) {
            Logger::logError('Failed to run task runner. Runner status storage unavailable.', 'Core', ['ExceptionMessage' => $ex->getMessage()]);
            Logger::logDebug('Failed to run task runner. Runner status storage unavailable.', 'Core', ['ExceptionMessage' => $ex->getMessage(), 'ExceptionTrace' => $ex->getTraceAsString()]);
        } catch (TaskRunnerRunException $ex) {
            Logger::logInfo($ex->getMessage());
            Logger::logDebug($ex->getMessage(), 'Core', ['ExceptionTrace' => $ex->getTraceAsString()]);
        } catch (Exception $ex) {
            Logger::logError('Failed to run task runner. Unexpected error occurred.', 'Core', ['ExceptionMessage' => $ex->getMessage()]);
            Logger::logDebug('Failed to run task runner. Unexpected error occurred.', 'Core', ['ExceptionMessage' => $ex->getMessage(), 'ExceptionTrace' => $ex->getTraceAsString()]);
        }
    }
    /**
     * Runs task execution.
     *
     * @throws TaskRunnerRunException
     * @throws TaskRunnerStatusStorageUnavailableException
     */
    private function doRun()
    {
        $runnerStatus = $this->getRunnerStorage()->getStatus();
        if ($this->guid !== $runnerStatus->getGuid()) {
            throw new TaskRunnerRunException('Failed to run task runner. Runner guid is not set as active.');
        }
        if ($runnerStatus->isExpired()) {
            $this->getTaskWakeup()->wakeup();
            throw new TaskRunnerRunException('Failed to run task runner. Runner is expired.');
        }
        $this->getTaskRunner()->setGuid($this->guid);
        $this->getTaskRunner()->run();
        /** @var EventBus $eventBus */
        $eventBus = ServiceRegister::getService(EventBus::CLASS_NAME);
        $eventBus->fire(new TickEvent());
        // Send wakeup signal when runner is completed.
        $this->getTaskWakeup()->wakeup();
    }
    /**
     * Gets task runner status storage instance.
     *
     * @return TaskRunnerStatusStorage Instance of runner status storage service.
     */
    private function getRunnerStorage(): TaskRunnerStatusStorage
    {
        if ($this->runnerStatusStorage === null) {
            $this->runnerStatusStorage = ServiceRegister::getService(TaskRunnerStatusStorage::CLASS_NAME);
        }
        return $this->runnerStatusStorage;
    }
    /**
     * Gets task runner instance.
     *
     * @return TaskRunner Instance of runner service.
     */
    private function getTaskRunner(): TaskRunner
    {
        if ($this->taskRunner === null) {
            $this->taskRunner = ServiceRegister::getService(TaskRunner::CLASS_NAME);
        }
        return $this->taskRunner;
    }
    /**
     * Gets task runner wakeup instance.
     *
     * @return TaskRunnerWakeup Instance of runner wakeup service.
     */
    private function getTaskWakeup(): TaskRunnerWakeup
    {
        if ($this->taskWakeup === null) {
            $this->taskWakeup = ServiceRegister::getService(TaskRunnerWakeup::CLASS_NAME);
        }
        return $this->taskWakeup;
    }
}
