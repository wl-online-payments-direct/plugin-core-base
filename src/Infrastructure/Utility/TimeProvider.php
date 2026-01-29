<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Utility;

use DateTime;
/**
 * Class TimeProvider.
 *
 * @package OnlinePayments\Core\Infrastructure\Utility
 */
class TimeProvider
{
    /**
     * Fully qualified name of this class.
     */
    const CLASS_NAME = __CLASS__;
    /**
     * Singleton instance.
     *
     * @var ?TimeProvider
     */
    protected static ?TimeProvider $instance = null;
    /**
     * TimeProvider constructor
     */
    private function __construct()
    {
    }
    /**
     * Returns singleton instance of TimeProvider.
     *
     * @return TimeProvider An instance.
     */
    public static function getInstance(): TimeProvider
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    /**
     * Gets current time in default server timezone.
     *
     * @return DateTime Current time as @see \DateTime object.
     */
    public function getCurrentLocalTime(): DateTime
    {
        return new DateTime();
    }
    /**
     * Returns @param int $timestamp Timestamp in seconds.
     *
     * @return DateTime Object from timestamp.
     * @see \DateTime object from timestamp.
     *
     */
    public function getDateTime(int $timestamp): DateTime
    {
        return new DateTime("@{$timestamp}");
    }
    /**
     * Returns current timestamp in milliseconds
     *
     * @return int Current time in milliseconds.
     */
    public function getMillisecondsTimestamp(): int
    {
        return (int) round($this->getMicroTimestamp() * 1000);
    }
    /**
     * Returns current timestamp with microseconds (float value with microsecond precision)
     *
     * @return float Current timestamp as float value with microseconds.
     */
    public function getMicroTimestamp(): float
    {
        return microtime(\true);
    }
    /**
     * Delays execution for sleep time seconds.
     *
     * @param int $sleepTime Sleep time in seconds.
     */
    public function sleep(int $sleepTime): void
    {
        sleep($sleepTime);
    }
    /**
     * Converts serialized string time to DateTime object.
     *
     * @param ?string $dateTime DateTime in string format.
     * @param string|null $format DateTime string format.
     *
     * @return DateTime | null Date or null.
     */
    public function deserializeDateString(?string $dateTime, string $format = null): ?DateTime
    {
        return $dateTime ? DateTime::createFromFormat($format ?: \DATE_ATOM, $dateTime) : null;
    }
    /**
     * Serializes date time object to its string format.
     *
     * @param DateTime|null $dateTime Date time object to be serialized.
     * @param string|null $format DateTime string format.
     *
     * @return string|null String serialized date.
     */
    public function serializeDate(DateTime $dateTime = null, string $format = null): ?string
    {
        if ($dateTime === null) {
            return null;
        }
        return $dateTime->format($format ?: \DATE_ATOM);
    }
    /**
     * @param DateTime $time
     *
     * @return void
     */
    public function setCurrentLocalTime(DateTime $time): void
    {
        // Nothing to do
    }
}
