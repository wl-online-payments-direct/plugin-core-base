<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Configuration;

use WOP\OnlinePayments\Core\Infrastructure\Http\DTO\Options;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\Singleton;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Exceptions\TaskRunnerStatusStorageUnavailableException;
/**
 * Class Configuration.
 *
 * @package OnlinePayments\Core\Infrastructure\Configuration
 */
abstract class Configuration extends Singleton
{
    /**
     * Fully qualified name of this interface.
     */
    const CLASS_NAME = __CLASS__;
    /**
     * Minimal log level
     */
    const MIN_LOG_LEVEL = 2;
    /**
     * Default maximum number of tasks that can run in the same time
     */
    const DEFAULT_MAX_STARTED_TASK_LIMIT = 64;
    /**
     * Default batch size for the asynchronous execution.
     */
    const DEFAULT_ASYNC_STARTER_BATCH_SIZE = 8;
    /**
     * Default HTTP method to use for async call.
     */
    const ASYNC_CALL_METHOD = 'POST';
    /**
     * List of global (non-user specific) values
     *
     * @var string[]
     */
    protected static array $globalConfigValues = ['taskRunnerStatus', 'isTaskRunnerHalted', 'maxTaskInactivityPeriod', 'maxTaskExecutionRetries', 'maxStartedTasksLimit', 'taskRunnerMaxAliveTime', 'taskRunnerWakeupDelay', 'asyncStarterBatchSize', 'asyncRequestTimeout', 'syncRequestTimeout', 'asyncRequestWithProgress'];
    /**
     * Singleton instance of this class.
     *
     * @var ?Singleton
     */
    protected static ?Singleton $instance;
    /**
     * Instance of the configuration manager.
     *
     * @var ?ConfigurationManager Configuration manager.
     */
    protected ?ConfigurationManager $configurationManager = null;
    /**
     * Retrieves integration name.
     *
     * @return string Integration name.
     */
    abstract public function getIntegrationName(): string;
    /**
     * Returns async process starter url, always in http.
     *
     * @param string $guid Process identifier.
     *
     * @return string Formatted URL of async process starter endpoint.
     */
    abstract public function getAsyncProcessUrl(string $guid): string;
    /**
     * Saves min log level in integration database.
     *
     * @param int $minLogLevel Min log level.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function saveMinLogLevel(int $minLogLevel): void
    {
        $this->saveConfigValue('minLogLevel', $minLogLevel);
    }
    /**
     * Retrieves min log level from integration database.
     *
     * @return int Min log level.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getMinLogLevel(): int
    {
        return $this->getConfigValue('minLogLevel', static::MIN_LOG_LEVEL);
    }
    /**
     * Set default logger status (enabled/disabled).
     *
     * @param bool $status TRUE if default logger is enabled; otherwise, false.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setDefaultLoggerEnabled(bool $status)
    {
        $this->saveConfigValue('defaultLoggerEnabled', $status);
    }
    /**
     * Return whether default logger is enabled or not.
     *
     * @return bool TRUE if default logger is enabled; otherwise, false.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function isDefaultLoggerEnabled(): bool
    {
        return $this->getConfigValue('defaultLoggerEnabled', \false);
    }
    /**
     * Sets debug mode status (enabled/disabled).
     *
     * @param bool $status TRUE if debug mode is enabled; otherwise, false.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setDebugModeEnabled(bool $status)
    {
        $this->saveConfigValue('debugModeEnabled', (bool) $status);
    }
    /**
     * Returns debug mode status.
     *
     * @return bool TRUE if debug mode is enabled; otherwise, false.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function isDebugModeEnabled(): bool
    {
        return $this->getConfigValue('debugModeEnabled', \false);
    }
    /**
     * Returns synchronous process timeout in milliseconds.
     *
     * @return int|null
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getSyncRequestTimeout(): ?int
    {
        return $this->getConfigValue('syncRequestTimeout');
    }
    /**
     * Gets maximal time in seconds allowed for runner instance to stay in alive (running) status. After this period
     * system will automatically start new runner instance and shutdown old one. Return null to use default system
     * value (60).
     *
     * @return int|null Task runner max alive time in seconds if set; otherwise, null;
     * @throws QueryFilterInvalidParamException
     */
    public function getTaskRunnerMaxAliveTime()
    {
        return $this->getConfigValue('taskRunnerMaxAliveTime');
    }
    /**
     * Sets max alive time.
     *
     * @param int $maxAliveTime Max alive time in seconds.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setTaskRunnerMaxAliveTime(int $maxAliveTime)
    {
        $this->saveConfigValue('taskRunnerMaxAliveTime', $maxAliveTime);
    }
    /**
     * Sets synchronous process timeout in milliseconds.
     *
     * @param int $timeout
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setSyncRequestTimeout(int $timeout): void
    {
        $this->saveConfigValue('syncRequestTimeout', $timeout);
    }
    /**
     * Returns async process timeout in milliseconds.
     *
     * @return int|null
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getAsyncRequestTimeout(): ?int
    {
        return $this->getConfigValue('asyncRequestTimeout');
    }
    /**
     * Sets async process timeout in milliseconds.
     *
     * @param int $timeout
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setAsyncRequestTimeout(int $timeout): void
    {
        $this->saveConfigValue('asyncRequestTimeout', $timeout);
    }
    /**
     * Gets auto-configuration controller URL.
     *
     * @return ?string Auto-configuration URL.
     */
    public function getAutoConfigurationUrl(): ?string
    {
        return $this->getAsyncProcessUrl('auto-configure');
    }
    /**
     * Sets the HTTP method to be used for the async call.
     *
     * @param string $method Http method (GET or POST).
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setAsyncProcessCallHttpMethod(string $method): void
    {
        $this->saveConfigValue('asyncProcessCallHttpMethod', $method);
    }
    /**
     * Sets config value for task runner halted flag.
     *
     * @param bool $isHalted Flag that indicates whether task runner is halted.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setTaskRunnerHalted(bool $isHalted): void
    {
        $this->saveConfigValue('isTaskRunnerHalted', $isHalted);
    }
    /**
     * Retrieves config value that indicates whether task runner is halted or not.
     *
     * @return bool Task runner halted status.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function isTaskRunnerHalted(): bool
    {
        return (bool) $this->getConfigValue('isTaskRunnerHalted', \false);
    }
    /**
     * Gets the number of maximum allowed started task at the point in time. This number will determine how many tasks
     * can be in "in_progress" status at the same time.
     *
     * @return int Max started tasks limit.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getMaxStartedTasksLimit(): int
    {
        return $this->getConfigValue('maxStartedTasksLimit', static::DEFAULT_MAX_STARTED_TASK_LIMIT);
    }
    /**
     * Returns task runner status information
     *
     * @return array Guid and timestamp information
     * @throws QueryFilterInvalidParamException
     */
    public function getTaskRunnerStatus(): array
    {
        return $this->getConfigValue('taskRunnerStatus', []);
    }
    /**
     * Sets task runner status information as JSON encoded string.
     *
     * @param string $guid Global unique identifier.
     * @param ?int $timestamp Timestamp.
     *
     * @throws TaskRunnerStatusStorageUnavailableException
     * @throws QueryFilterInvalidParamException
     */
    public function setTaskRunnerStatus(string $guid, ?int $timestamp): void
    {
        $taskRunnerStatus = ['guid' => $guid, 'timestamp' => $timestamp];
        $config = $this->saveConfigValue('taskRunnerStatus', $taskRunnerStatus);
        if (!$config || !$config->getId()) {
            throw new TaskRunnerStatusStorageUnavailableException('Task runner status storage is not available.');
        }
    }
    /**
     * Gets maximum number of failed task execution retries. System will retry task execution in case of error until
     * this number is reached. Return null to use default system value (5).
     *
     * @return int|null Number of max execution retries if set; otherwise, false.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getMaxTaskExecutionRetries(): ?int
    {
        return $this->getConfigValue('maxTaskExecutionRetries');
    }
    /**
     * Sets max task execution retries.
     *
     * @param int $maxRetries Max number of retries.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setMaxTaskExecutionRetries(int $maxRetries)
    {
        $this->saveConfigValue('maxTaskExecutionRetries', $maxRetries);
    }
    /**
     * Gets max inactivity period for a task in seconds. After inactivity period is passed, system will fail such tasks
     * as expired. Return null to use default system value (30).
     *
     * @return int|null Max task inactivity period in seconds if set; otherwise, null.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getMaxTaskInactivityPeriod(): ?int
    {
        return $this->getConfigValue('maxTaskInactivityPeriod');
    }
    /**
     * Sets max task inactivity period.
     *
     * @param int $maxInactivityPeriod Max inactivity period in seconds.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setMaxTaskInactivityPeriod(int $maxInactivityPeriod)
    {
        $this->saveConfigValue('maxTaskInactivityPeriod', $maxInactivityPeriod);
    }
    /**
     * Automatic task runner wakeup delay in seconds. Task runner will sleep at the end of its lifecycle for this value
     * seconds before it sends wakeup signal for a new lifecycle. Return null to use default system value (10).
     *
     * @return int|null Task runner wakeup delay in seconds if set; otherwise, null.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getTaskRunnerWakeupDelay(): ?int
    {
        return $this->getConfigValue('taskRunnerWakeupDelay');
    }
    /**
     * Sets task runner wakeup delay.
     *
     * @param int $delay Delay in seconds.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setTaskRunnerWakeupDelay(int $delay)
    {
        $this->saveConfigValue('taskRunnerWakeupDelay', $delay);
    }
    /**
     * Sets the number of maximum allowed started task at the point in time. This number will determine how many tasks
     * can be in "in_progress" status at the same time.
     *
     * @param int $limit Max started tasks limit.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setMaxStartedTasksLimit(int $limit)
    {
        $this->saveConfigValue('maxStartedTasksLimit', $limit);
    }
    /**
     * Retrieves async starter batch size.
     *
     * @return int Async starter batch size.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getAsyncStarterBatchSize(): int
    {
        return $this->getConfigValue('asyncStarterBatchSize', static::DEFAULT_ASYNC_STARTER_BATCH_SIZE);
    }
    /**
     * Sets async process batch size.
     *
     * @param int $size
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setAsyncStarterBatchSize(int $size)
    {
        $this->saveConfigValue('asyncStarterBatchSize', $size);
    }
    /**
     * Returns current HTTP method used for the async call.
     *
     * @return string The async call HTTP method (GET or POST).
     * @throws QueryFilterInvalidParamException
     */
    public function getAsyncProcessCallHttpMethod(): string
    {
        return $this->getConfigValue('asyncProcessCallHttpMethod', static::ASYNC_CALL_METHOD);
    }
    /**
     * Sets current auto-configuration state.
     *
     * @param string $state Current state.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setAutoConfigurationState(string $state): void
    {
        $this->saveConfigValue('autoConfigurationState', $state);
    }
    /**
     * Save config value.
     *
     * @param string $name Name of the configuration value.
     * @param mixed $value Configuration value.
     *
     * @return ConfigEntity
     *
     * @throws QueryFilterInvalidParamException
     */
    protected function saveConfigValue(string $name, $value): ConfigEntity
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->getConfigurationManager()->saveConfigValue($name, $value, $this->isContextSpecific($name));
    }
    /**
     * Retrieves saved config value.
     *
     * @param string $name Config value name.
     * @param mixed $default Default config value.
     *
     * @return mixed Config value.
     *
     * @throws QueryFilterInvalidParamException
     */
    protected function getConfigValue(string $name, $default = null)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->getConfigurationManager()->getConfigValue($name, $default, $this->isContextSpecific($name));
    }
    /**
     * Removes configuration entity.
     *
     * @param string $name Config value name.
     *
     * @throws QueryFilterInvalidParamException
     */
    protected function deleteConfig(string $name): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->getConfigurationManager()->deleteConfigEntity($name, $this->isContextSpecific($name));
    }
    /**
     * Gets current auto-configuration state.
     *
     * @return string Current state.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getAutoConfigurationState(): string
    {
        return $this->getConfigValue('autoConfigurationState', '');
    }
    /**
     * Gets current HTTP configuration options for given domain.
     *
     * @param string $domain A domain for which to return configuration options.
     *
     * @return Options[]
     * @throws QueryFilterInvalidParamException
     */
    public function getHttpConfigurationOptions(string $domain): array
    {
        $data = json_decode($this->getConfigValue('httpConfigurationOptions', '[]'), \true);
        if (isset($data[$domain])) {
            return Options::fromBatch($data[$domain]);
        }
        return [];
    }
    /**
     * Sets HTTP configuration options for given domain.
     *
     * @param string $domain A domain for which to save configuration options.
     *
     * @param Options[] $options HTTP configuration options
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setHttpConfigurationOptions(string $domain, array $options)
    {
        // get all current options and append new ones for given domain
        $data = json_decode($this->getConfigValue('httpConfigurationOptions', '[]'), \true);
        $data[$domain] = [];
        foreach ($options as $option) {
            $data[$domain][] = $option->toArray();
        }
        $this->saveConfigValue('httpConfigurationOptions', json_encode($data));
    }
    /**
     * Sets the auto-test mode flag.
     *
     * @param bool $status
     */
    public function setAutoTestMode(bool $status)
    {
        $this->saveConfigValue('autoTestMode', $status);
    }
    /**
     * Returns whether the auto-test mode is active.
     *
     * @return bool TRUE if the auto-test mode is active; otherwise, FALSE.
     */
    public function isAutoTestMode(): bool
    {
        return (bool) $this->getConfigValue('autoTestMode', \false);
    }
    /**
     * Determines whether the configuration entry is system specific.
     *
     * @param string $name Configuration entry name.
     *
     * @return bool
     */
    protected function isContextSpecific(string $name): bool
    {
        return !in_array($name, static::$globalConfigValues, \true);
    }
    /**
     * Retrieves configuration manager.
     *
     * @return ConfigurationManager Configuration manager instance.
     */
    protected function getConfigurationManager(): ConfigurationManager
    {
        if ($this->configurationManager === null) {
            $this->configurationManager = ServiceRegister::getService(ConfigurationManager::CLASS_NAME);
        }
        return $this->configurationManager;
    }
}
