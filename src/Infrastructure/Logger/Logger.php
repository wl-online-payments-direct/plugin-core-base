<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Logger;

use WOP\OnlinePayments\Core\Infrastructure\Logger\Interfaces\DefaultLoggerAdapter;
use WOP\OnlinePayments\Core\Infrastructure\Logger\Interfaces\ShopLoggerAdapter;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\Singleton;
use WOP\OnlinePayments\Core\Infrastructure\Utility\TimeProvider;
/**
 * Class Logger.
 *
 * @package OnlinePayments\Core\Infrastructure\Logger
 */
class Logger extends Singleton
{
    /**
     * Error type of log.
     */
    const ERROR = 0;
    /**
     * Warning type of log.
     */
    const WARNING = 1;
    /**
     * Info type of log.
     */
    const INFO = 2;
    /**
     * Debug type of log.
     */
    const DEBUG = 3;
    /**
     * Singleton instance of this class.
     *
     * @var ?Singleton
     */
    protected static ?Singleton $instance = null;
    /**
     * Shop logger.
     *
     * @var ShopLoggerAdapter
     */
    private $shopLogger;
    /**
     * Time provider.
     *
     * @var TimeProvider
     */
    private $timeProvider;
    /**
     * Logger constructor. Hidden.
     */
    protected function __construct()
    {
        parent::__construct();
        $this->shopLogger = ServiceRegister::getService(ShopLoggerAdapter::CLASS_NAME);
        $this->timeProvider = ServiceRegister::getService(TimeProvider::CLASS_NAME);
    }
    /**
     * Logs error message.
     *
     * @param string $message Message to log.
     * @param string $component Component for which to log message.
     * @param LogContextData[]|array<string, string> $context Additional context data.
     *
     * @return void
     */
    public static function logError(string $message, string $component = 'Core', array $context = [])
    {
        self::getInstance()->logMessage(self::ERROR, $message, $component, $context);
    }
    /**
     * Logs warning message.
     *
     * @param string $message Message to log.
     * @param string $component Component for which to log message.
     * @param LogContextData[]|array $context Additional context data.
     *
     * @return void
     */
    public static function logWarning(string $message, string $component = 'Core', array $context = []): void
    {
        self::getInstance()->logMessage(self::WARNING, $message, $component, $context);
    }
    /**
     * Logs info message.
     *
     * @param string $message Message to log.
     * @param string $component Component for which to log message.
     * @param LogContextData[]|array $context Additional context data.
     *
     * @return void
     */
    public static function logInfo(string $message, string $component = 'Core', array $context = []): void
    {
        self::getInstance()->logMessage(self::INFO, $message, $component, $context);
    }
    /**
     * Logs debug message.
     *
     * @param string $message Message to log.
     * @param string $component Component for which to log message.
     * @param LogContextData[]|array $context Additional context data.
     */
    public static function logDebug(string $message, string $component = 'Core', array $context = [])
    {
        self::getInstance()->logMessage(self::DEBUG, $message, $component, $context);
    }
    /**
     * Logs message.
     *
     * @param int $level Log level.
     * @param string $message Message to log.
     * @param string $component Component for which to log message.
     * @param LogContextData[]|array $context Additional context data.
     *
     * @return void
     */
    protected function logMessage(int $level, string $message, string $component, array $context = []): void
    {
        $config = LoggerConfiguration::getInstance();
        $logData = new LogData($config->getIntegrationName(), $level, $this->timeProvider->getMillisecondsTimestamp(), $component, $message, $context);
        // If default logger is turned on and message level is lower or equal than set in configuration
        if ($config->isDefaultLoggerEnabled() && $level <= $config->getMinLogLevel()) {
            $defaultLogger = ServiceRegister::getService(DefaultLoggerAdapter::CLASS_NAME);
            $defaultLogger->logMessage($logData);
        }
        $this->shopLogger->logMessage($logData);
    }
}
