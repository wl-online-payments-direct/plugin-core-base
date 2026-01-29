<?php

namespace WOP\OnlinePayments\Core\Bootstrap\Sdk;

use Exception;
use WOP\OnlinePayments\Core\Infrastructure\Logger\Logger;
use OnlinePayments\Sdk\Logging\CommunicatorLogger;
class ApiLogger implements CommunicatorLogger
{
    public function log($message): void
    {
        Logger::logInfo($message);
    }
    public function logException($message, Exception $exception): void
    {
        Logger::logError($message, 'Core', ['message' => $exception->getMessage(), 'type' => get_class($exception), 'trace' => $exception->getTraceAsString()]);
    }
}
