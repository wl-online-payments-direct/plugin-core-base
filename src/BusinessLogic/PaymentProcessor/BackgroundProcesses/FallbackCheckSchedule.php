<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\BackgroundProcesses;

use DateTime;
/**
 * Class FallbackCheckSchedule.
 *
 * Degressive re-check schedule for the background fallback cron: checks a still-pending transaction
 * frequently right after checkout, when a status change is most likely, and backs off the longer it
 * stays unresolved. The last interval repeats indefinitely once the schedule is exhausted.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\BackgroundProcesses
 */
class FallbackCheckSchedule
{
    private const INTERVALS_IN_MINUTES = [1, 2, 5, 10, 15, 30];
    /**
     * Minutes the outer task-runner listener should use as its own throttle, so it runs often enough
     * to honor the finest-grained step of this schedule.
     */
    public static function minimumIntervalInMinutes(): int
    {
        return self::INTERVALS_IN_MINUTES[0];
    }
    /**
     * Whether a transaction that has already been fallback-checked $attemptsSoFar times, created at
     * $createdAt, is due for another check at $now.
     */
    public static function isDue(?DateTime $createdAt, int $attemptsSoFar, DateTime $now): bool
    {
        if (null === $createdAt) {
            return \true;
        }
        return $now->getTimestamp() >= $createdAt->getTimestamp() + self::cumulativeDelayInSeconds($attemptsSoFar);
    }
    private static function cumulativeDelayInSeconds(int $attemptsSoFar): int
    {
        $lastIndex = count(self::INTERVALS_IN_MINUTES) - 1;
        $totalMinutes = 0;
        for ($attempt = 0; $attempt <= $attemptsSoFar; $attempt++) {
            $totalMinutes += self::INTERVALS_IN_MINUTES[min($attempt, $lastIndex)];
        }
        return $totalMinutes * 60;
    }
}
