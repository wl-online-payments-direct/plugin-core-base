<?php

namespace WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces;

use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Exceptions\ProcessStarterSaveException;
/**
 * Interface AsyncProcessStarter.
 *
 * @package OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces
 */
interface AsyncProcessService
{
    /**
     * Fully qualified name of this interface.
     */
    const CLASS_NAME = __CLASS__;
    /**
     * Starts given runner asynchronously (in new process/web request or similar)
     *
     * @param Runnable $runner Runner that should be started async
     *
     * @throws ProcessStarterSaveException
     */
    public function start(Runnable $runner);
    /**
     * Runs a process with provided identifier.
     *
     * @param string $guid Identifier of process.
     */
    public function runProcess(string $guid);
}
