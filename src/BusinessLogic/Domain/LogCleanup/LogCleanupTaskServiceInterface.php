<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\LogCleanup;

/**
 * Interface LogCleanupTaskServiceInterface
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\LogCleanup
 */
interface LogCleanupTaskServiceInterface
{
    /**
     * @return int
     */
    public function findLatestExecutionTimestamp(): int;
    /**
     * @return void
     */
    public function enqueueLogCleanupTask(): void;
}
