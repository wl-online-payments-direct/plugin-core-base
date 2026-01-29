<?php

namespace WOP\OnlinePayments\Core\Infrastructure\AutoTest;

use WOP\OnlinePayments\Core\Infrastructure\Data\DataTransferObject;
use WOP\OnlinePayments\Core\Infrastructure\Logger\LogData;
/**
 * Class AutoTestStatus.
 *
 * @package OnlinePayments\Core\Infrastructure\AutoTest
 */
class AutoTestStatus extends DataTransferObject
{
    /**
     * The current status of the auto-test task.
     *
     * @var string
     */
    public string $taskStatus;
    /**
     * Indicates whether the task finished.
     *
     * @var bool
     */
    public bool $finished;
    /**
     * Error message, if any.
     *
     * @var string
     */
    public string $error;
    /**
     * An array of logs.
     *
     * @var LogData[]
     */
    public array $logs;
    /**
     * AutoTestStatus constructor.
     *
     * @param string $taskStatus The current status of the auto-test task.
     * @param bool $finished Indicates whether the task finished.
     * @param string $error Error message, if any.
     * @param LogData[] $logs An array of logs.
     */
    public function __construct(string $taskStatus, bool $finished, string $error, array $logs)
    {
        $this->taskStatus = $taskStatus;
        $this->finished = $finished;
        $this->error = $error;
        $this->logs = $logs;
    }
    /**
     * Returns an array representation of the object.
     *
     * @return array This object as an array.
     */
    public function toArray(): array
    {
        return array('taskStatus' => $this->taskStatus, 'finished' => $this->finished, 'error' => $this->error, 'logs' => $this->logs);
    }
    /**
     * @inheritDoc
     */
    public static function fromArray(array $data): DataTransferObject
    {
        return new self($data['taskStatus'], $data['finished'], $data['error'], $data['logs']);
    }
}
