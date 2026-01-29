<?php

namespace WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces;

use WOP\OnlinePayments\Core\Infrastructure\Serializer\Interfaces\Serializable;
/**
 * Interface Runnable.
 *
 * @package OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces
 */
interface Runnable extends Serializable
{
    /**
     * Starts runnable run logic
     */
    public function run();
}
