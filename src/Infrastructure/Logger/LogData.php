<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Logger;

use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
/**
 * Class LogData.
 *
 * @package OnlinePayments\Core\Infrastructure\Logger
 */
class LogData extends Entity
{
    /**
     * Fully qualified name of this class.
     */
    const CLASS_NAME = __CLASS__;
    /**
     * Name of the integration.
     *
     * @var string
     */
    protected string $integration;
    /**
     * Array of LogContextData.
     *
     * @var LogContextData[]
     */
    protected array $context;
    /**
     * Log level.
     *
     * @var int
     */
    protected int $logLevel;
    /**
     * Log timestamp.
     *
     * @var int
     */
    protected int $timestamp;
    /**
     * Name of the component.
     *
     * @var string
     */
    protected string $component;
    /**
     * Log message.
     *
     * @var string
     */
    protected string $message;
    /**
     * Array of field names.
     *
     * @var array
     */
    protected array $fields = ['id', 'integration', 'logLevel', 'timestamp', 'component', 'message'];
    /**
     * LogData constructor.
     *
     * @param string $integration Name of integration.
     * @param int $logLevel Log level. Use constants in @see Logger class.
     * @param int $timestamp Log timestamp.
     * @param string $component Name of the log component.
     * @param string $message Log message.
     * @param LogContextData[]|array $context Log contexts as an array of @see LogContextData or as key value entries.
     */
    public function __construct(string $integration = '', int $logLevel = Logger::ERROR, int $timestamp = 0, string $component = '', string $message = '', array $context = [])
    {
        $this->integration = $integration;
        $this->logLevel = $logLevel;
        $this->component = $component;
        $this->timestamp = $timestamp;
        $this->message = $message;
        $this->context = [];
        foreach ($context as $key => $item) {
            if (!$item instanceof LogContextData) {
                $item = new LogContextData($key, $item);
            }
            $this->context[] = $item;
        }
    }
    /**
     * Returns entity configuration object.
     *
     * @return EntityConfiguration Configuration object.
     */
    public function getConfig(): EntityConfiguration
    {
        $map = new IndexMap();
        $map->addStringIndex('integration')->addIntegerIndex('logLevel')->addIntegerIndex('timestamp')->addStringIndex('component');
        return new EntityConfiguration($map, 'LogData');
    }
    /**
     * Transforms raw array data to this entity instance.
     *
     * @param array $data Raw array data.
     */
    public function inflate(array $data)
    {
        parent::inflate($data);
        $context = !empty($data['context']) ? $data['context'] : [];
        $this->context = [];
        foreach ($context as $key => $value) {
            $item = new LogContextData($key, $value);
            $this->context[] = $item;
        }
    }
    /**
     * Transforms entity to its array format representation.
     *
     * @return array Entity in array format.
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        foreach ($this->context as $item) {
            $data['context'][$item->getName()] = $item->getValue();
        }
        return $data;
    }
    /**
     * Gets name of the integration.
     *
     * @return string Name of the integration.
     */
    public function getIntegration(): string
    {
        return $this->integration;
    }
    /**
     * Gets context data array.
     *
     * @return LogContextData[] Array of LogContextData.
     */
    public function getContext(): array
    {
        return $this->context;
    }
    /**
     * Gets log level.
     *
     * @return int
     *   Log level:
     *    - error => 0
     *    - warning => 1
     *    - info => 2
     *    - debug => 3
     */
    public function getLogLevel(): int
    {
        return $this->logLevel;
    }
    /**
     * Gets timestamp in seconds.
     *
     * @return int Log timestamp.
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }
    /**
     * Gets log component.
     *
     * @return string Log component.
     */
    public function getComponent(): string
    {
        return $this->component;
    }
    /**
     * Gets log message.
     *
     * @return string Log message.
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
