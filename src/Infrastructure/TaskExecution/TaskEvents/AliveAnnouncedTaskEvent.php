<?php

namespace WOP\OnlinePayments\Core\Infrastructure\TaskExecution\TaskEvents;

use WOP\OnlinePayments\Core\Infrastructure\Utility\Events\Event;
/**
 * Class AliveAnnouncedTaskEvent.
 *
 * @package OnlinePayments\Core\Infrastructure\TaskExecution\TaskEvents
 */
class AliveAnnouncedTaskEvent extends Event
{
    /**
     * Fully qualified name of this class.
     */
    const CLASS_NAME = __CLASS__;
}
