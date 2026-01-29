<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Utility;

/**
 * Class GuidProvider.
 *
 * @package OnlinePayments\Core\Infrastructure\Utility
 */
class GuidProvider
{
    /**
     * Fully qualified name of this class.
     */
    const CLASS_NAME = __CLASS__;
    /**
     * Singleton instance of this class.
     *
     * @var ?GuidProvider
     */
    protected static ?GuidProvider $instance = null;
    /**
     * GuidProvider constructor.
     */
    private function __construct()
    {
    }
    /**
     * Returns singleton instance of GuidProvider.
     *
     * @return GuidProvider Instance of GuidProvider class.
     */
    public static function getInstance(): GuidProvider
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    /**
     * Generates random string.
     *
     * @return string Generated string.
     */
    public function generateGuid(): string
    {
        return uniqid(getmypid() . '_', \true);
    }
}
