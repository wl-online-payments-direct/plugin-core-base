<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck;

use Throwable;
/**
 * Interface ApiFailureClassifierInterface
 *
 * Decides whether a failure raised by a Worldline API call is a known negative - an expected, definitive
 * "no" such as invalid credentials or an inactive merchant account - or a technical error that a retry
 * could plausibly recover from. Implemented outside the business logic because only the transport layer
 * knows how the SDK surfaces HTTP statuses and connectivity failures.
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck
 */
interface ApiFailureClassifierInterface
{
    /**
     * @param Throwable $failure
     *
     * @return bool True when the failure is a known negative and must not be retried.
     */
    public function isKnownNegative(Throwable $failure): bool;
}
