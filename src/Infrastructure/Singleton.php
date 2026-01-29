<?php

namespace WOP\OnlinePayments\Core\Infrastructure;

use RuntimeException;
/**
 * Class Singleton.
 * Base class for all singleton implementations.
 * Every class that extends this class MUST have its own protected static field $instance!
 *
 * @package OnlinePayments\Core\Infrastructure
 */
abstract class Singleton
{
    /** @var ?Singleton */
    protected static ?Singleton $instance = null;
    /**
     * Hidden constructor.
     */
    protected function __construct()
    {
    }
    /**
     * Returns singleton instance of callee class.
     *
     * @return static Instance of callee class.
     */
    public static function getInstance(): Singleton
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        if (!static::$instance instanceof static) {
            throw new RuntimeException('Invalid singleton instance.');
        }
        return static::$instance;
    }
    /**
     * Resets singleton instance. Required for proper tests.
     */
    public static function resetInstance(): void
    {
        static::$instance = null;
    }
}
