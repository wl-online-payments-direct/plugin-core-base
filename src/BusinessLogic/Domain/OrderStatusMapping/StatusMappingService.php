<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\OrderStatusMapping;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\GeneralSettingsService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidAutomaticCaptureValueException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPaymentAttemptsNumberException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentDetails;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusCode;
/**
 * Class StatusMappingService
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\OrderStatusMapping
 */
class StatusMappingService
{
    protected GeneralSettingsService $generalSettingsService;
    /**
     * @param GeneralSettingsService $generalSettingsService
     */
    public function __construct(GeneralSettingsService $generalSettingsService)
    {
        $this->generalSettingsService = $generalSettingsService;
    }
    /**
     * @param PaymentDetails $paymentDetails
     *
     * @return string
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     */
    public function getFullStatusMapping(PaymentDetails $paymentDetails): string
    {
        $mapping = $this->generalSettingsService->getPaymentSettings();
        $mappedStatus = $this->getStatusMapping($paymentDetails->getStatusCode());
        if ($paymentDetails->getAmounts() && $paymentDetails->getAmounts()->getRefundedAmount()->getValue() > 0 && $paymentDetails->getAmounts()->getRefundedAmount()->getValue() < $paymentDetails->getAmount()->getValue() && !empty($mapping->getPaymentRefundedStatus())) {
            return $mapping->getPaymentPartiallyRefundedStatus();
        }
        return $mappedStatus;
    }
    public function getStatusMapping(StatusCode $statusCode): string
    {
        $mapping = $this->generalSettingsService->getPaymentSettings();
        if ($statusCode->equals(StatusCode::completed())) {
            return $mapping->getPaymentCapturedStatus();
        }
        if ($statusCode->equals(StatusCode::authorized())) {
            return $mapping->getPaymentAuthorizedStatus();
        }
        if ($statusCode->isCanceledOrRejected()) {
            return $mapping->getPaymentCancelledStatus();
        }
        if ($statusCode->isRefunded()) {
            return $mapping->getPaymentRefundedStatus();
        }
        if ($statusCode->isPending()) {
            return $mapping->getPaymentPendingStatus();
        }
        return '';
    }
}
