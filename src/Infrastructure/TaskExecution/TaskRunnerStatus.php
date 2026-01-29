<?php

namespace WOP\OnlinePayments\Core\Infrastructure\TaskExecution;

use WOP\OnlinePayments\Core\Infrastructure\Configuration\Configuration;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\Utility\TimeProvider;
/**
 * Class TaskRunnerStatus
 * @package OnlinePayments\Core\Infrastructure\TaskExecution
 */
class TaskRunnerStatus
{
    /**
     * Maximal time allowed for runner instance to stay in alive (running) status in seconds
     */
    const MAX_ALIVE_TIME = 15;
    /**
     * Identifier of task runner.
     *
     * @var string
     */
    private string $guid;
    /**
     * Timestamp since task runner is alive.
     *
     * @var int|null
     */
    private ?int $aliveSinceTimestamp;
    /**
     * Time provider service instance.
     *
     * @var TimeProvider
     */
    private $timeProvider;
    /**
     * Configuration service instance.
     *
     * @var Configuration
     */
    private $configService;
    /**
     * TaskRunnerStatus constructor.
     *
     * @param string $guid Runner instance identifier.
     * @param ?int $aliveSinceTimestamp Timestamp of last alive moment.
     */
    public function __construct(string $guid, ?int $aliveSinceTimestamp)
    {
        $this->guid = $guid;
        $this->aliveSinceTimestamp = $aliveSinceTimestamp;
        $this->timeProvider = ServiceRegister::getService(TimeProvider::CLASS_NAME);
        $this->configService = ServiceRegister::getService(Configuration::CLASS_NAME);
    }
    /**
     * Creates empty status object.
     *
     * @return TaskRunnerStatus Empty status object.
     */
    public static function createNullStatus(): TaskRunnerStatus
    {
        return new self('', null);
    }
    /**
     * Gets runner instance identifier.
     *
     * @return string Instance identifier.
     */
    public function getGuid(): string
    {
        return $this->guid;
    }
    /**
     * Gets timestamp since runner is in alive status.
     *
     * @return int|null Timestamp since runner is in alive status; otherwise, NULL.
     */
    public function getAliveSinceTimestamp(): ?int
    {
        return $this->aliveSinceTimestamp;
    }
    /**
     * Checks if task is expired.
     *
     * @return bool TRUE if task expired; otherwise, FALSE.
     */
    public function isExpired(): bool
    {
        $currentTimestamp = $this->timeProvider->getCurrentLocalTime()->getTimestamp();
        return $this->aliveSinceTimestamp > 0 && $this->aliveSinceTimestamp + $this->getMaxAliveTimestamp() < $currentTimestamp;
    }
    /**
     * Retrieves max alive timestamp.
     *
     * @return int Max alive timestamp.
     */
    private function getMaxAliveTimestamp(): int
    {
        $configurationValue = $this->configService->getTaskRunnerMaxAliveTime();
        return $configurationValue !== null ? $configurationValue : self::MAX_ALIVE_TIME;
    }
}
