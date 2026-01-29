<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Time;

/**
 * Interface TimeProviderInterface.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Time
 */
interface TimeProviderInterface
{
    /**
     * Gets current time in default server timezone.
     *
     * @return \DateTime Current time as @see \DateTime object.
     */
    public function getCurrentLocalTime(): \DateTime;
    /**
     * Returns @param int $timestamp Timestamp in seconds.
     *
     * @return \DateTime Object from timestamp.
     * @see \DateTime object from timestamp.
     *
     */
    public function getDateTime(int $timestamp): \DateTime;
    /**
     * Returns current timestamp with microseconds (float value with microsecond precision)
     *
     * @return float Current timestamp as float value with microseconds.
     */
    public function getMicroTimestamp(): float;
    /**
     * Delays execution for sleep time seconds.
     *
     * @param int $sleepTime Sleep time in seconds.
     */
    public function sleep(int $sleepTime): void;
}
