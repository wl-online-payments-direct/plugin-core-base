<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Logger\Interfaces;

use WOP\OnlinePayments\Core\Infrastructure\Logger\LogData;
/**
 * Interface LoggerAdapter.
 *
 * @package OnlinePayments\Core\Infrastructure\Logger\Interfaces
 */
interface LoggerAdapter
{
    /**
     * Log message in system
     *
     * @param LogData $data
     *
     * @return void
     */
    public function logMessage(LogData $data): void;
}
