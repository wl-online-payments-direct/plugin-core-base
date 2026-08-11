<?php

namespace WOP\OnlinePayments\Core\Bootstrap\Sdk;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck\ApiFailureClassifierInterface;
use OnlinePayments\Sdk\ResponseException;
use Throwable;
/**
 * Class SdkApiFailureClassifier
 *
 * Classifies SDK failures by what the transport actually reports.
 *
 * The SDK throws a ResponseException (via ExceptionFactory) whenever Worldline answered with a non-2xx
 * status, and that exception carries the status code. A 4xx means Worldline understood the request and
 * refused it - invalid credentials, an inactive account - which no amount of retrying will change.
 *
 * Everything else is treated as technical and therefore retryable: 5xx platform errors, and connectivity
 * or timeout failures, which never reach ExceptionFactory at all because cURL fails first and
 * DefaultConnection raises a plain ErrorException.
 *
 * @package OnlinePayments\Core\Bootstrap\Sdk
 */
class SdkApiFailureClassifier implements ApiFailureClassifierInterface
{
    /**
     * @inheritDoc
     */
    public function isKnownNegative(Throwable $failure): bool
    {
        if (!$failure instanceof ResponseException) {
            return \false;
        }
        $statusCode = $failure->getHttpStatusCode();
        return $statusCode >= 400 && $statusCode < 500;
    }
}
