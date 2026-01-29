<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Logger;

use Exception;
use WOP\OnlinePayments\Core\Infrastructure\Configuration\Configuration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\Singleton;
/**
 * Class LoggerConfiguration.
 *
 * @package OnlinePayments\Core\Infrastructure\Logger
 */
class LoggerConfiguration extends Singleton
{
    /**
     * Default minimum level for logging.
     */
    const DEFAULT_MIN_LOG_LEVEL = Logger::DEBUG;
    /**
     * Identifies if default logger should be used by default.
     */
    const DEFAULT_IS_DEFAULT_LOGGER_ENABLED = \false;
    /**
     * Singleton instance of this class.
     *
     * @var ?LoggerConfiguration
     */
    protected static ?Singleton $instance = null;
    /**
     * Whether default logger is enabled or not.
     *
     * @var boolean
     */
    private bool $isDefaultLoggerEnabled;
    /**
     * Configuration service instance.
     *
     * @var ?Configuration
     */
    private ?Configuration $shopConfiguration = null;
    /**
     * Minimum log level set.
     *
     * @var ?int
     */
    private ?int $minLogLevel = null;
    /**
     * Integration name.
     *
     * @var string
     */
    private string $integrationName;
    /**
     * Set default logger status (turning on/off).
     *
     * @param bool $status
     */
    public static function setDefaultLoggerEnabled(bool $status)
    {
        self::getInstance()->setIsDefaultLoggerEnabled($status);
    }
    /**
     * Return whether default logger is enabled or not.
     *
     * @return bool
     *   Logger status true => enabled, false => disabled.
     */
    public function isDefaultLoggerEnabled(): bool
    {
        if (empty($this->isDefaultLoggerEnabled)) {
            try {
                $this->isDefaultLoggerEnabled = $this->getShopConfiguration()->isDefaultLoggerEnabled();
            } catch (Exception $ex) {
                // Catch if configuration is not set properly and for some reason throws exception
                // e.g. Client is still not authorized (meaning that configuration is not set)
                // and we want to log something
            }
        }
        return !empty($this->isDefaultLoggerEnabled) ? $this->isDefaultLoggerEnabled : self::DEFAULT_IS_DEFAULT_LOGGER_ENABLED;
    }
    /**
     * Set default logger status (enabled or disabled).
     *
     * @param bool $loggerStatus Logger status true => enabled, false => disabled.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setIsDefaultLoggerEnabled(bool $loggerStatus)
    {
        $this->getShopConfiguration()->setDefaultLoggerEnabled($loggerStatus);
        $this->isDefaultLoggerEnabled = $loggerStatus;
    }
    /**
     * Retrieves minimum log level set.
     *
     * @return int
     *   Log level:
     *    - error => 0
     *    - warning => 1
     *    - info => 2
     *    - debug => 3
     */
    public function getMinLogLevel(): int
    {
        if ($this->minLogLevel === null) {
            try {
                $this->minLogLevel = $this->getShopConfiguration()->getMinLogLevel();
            } catch (Exception $ex) {
                // Catch if configuration is not set properly and for some reason throws exception
                // e.g. Client is still not authorized (meaning that configuration is not set)
                // and we want to log something
            }
        }
        return $this->minLogLevel !== null ? $this->minLogLevel : self::DEFAULT_MIN_LOG_LEVEL;
    }
    /**
     * Saves min log level in integration.
     *
     * @param int $minLogLevel Log level.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function setMinLogLevel(int $minLogLevel): void
    {
        $this->getShopConfiguration()->saveMinLogLevel($minLogLevel);
        $this->minLogLevel = $minLogLevel;
    }
    /**
     * Retrieves integration name.
     *
     * @return string Integration name.
     */
    public function getIntegrationName(): string
    {
        if (empty($this->integrationName)) {
            try {
                $this->integrationName = $this->getShopConfiguration()->getIntegrationName();
            } catch (Exception $ex) {
                // Catch if configuration is not set properly and for some reason throws exception
                // e.g. Client is still not authorized (meaning that configuration is not set)
                // and we want to log something
            }
        }
        return !empty($this->integrationName) ? $this->integrationName : 'unknown';
    }
    /**
     * Gets instance of configuration service.
     *
     * @return Configuration Instance of configuration service.
     *
     */
    private function getShopConfiguration(): Configuration
    {
        if ($this->shopConfiguration === null) {
            $this->shopConfiguration = ServiceRegister::getService(Configuration::CLASS_NAME);
        }
        return $this->shopConfiguration;
    }
}
