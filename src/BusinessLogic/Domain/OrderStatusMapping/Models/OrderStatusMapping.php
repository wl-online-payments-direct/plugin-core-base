<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\OrderStatusMapping\Models;

/**
 * Class OrderStatusMapping
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\OrderStatusMapping\Models
 */
class OrderStatusMapping
{
    /**
     * Status 9.
     *
     * @var string
     */
    protected string $paymentCapturedStatus;
    /**
     * Statuses 1 and 2.
     *
     * @var string
     */
    protected string $paymentErrorStatus;
    /**
     * Statuses 0, 4, 46, 51, 52, 55, 91, 92, 99.
     *
     * @var string
     */
    protected string $paymentPendingStatus;
    /**
     * Statuses 5, 50.
     *
     * @var string
     */
    protected string $paymentAuthorizedStatus;
    /**
     * Statuses 6, 61, 62.
     *
     * @var string
     */
    protected string $paymentCancelledStatus;
    /**
     * Statuses 7, 8
     *
     * @var string
     */
    protected string $paymentRefundedStatus;
    /**
     * @var string
     */
    protected string $paymentPartiallyRefundedStatus;
    /**
     * @param string $paymentCapturedStatus
     * @param string $paymentErrorStatus
     * @param string $paymentPendingStatus
     * @param string $paymentAuthorizedStatus
     * @param string $paymentCancelledStatus
     * @param string $paymentRefundedStatus
     * @param string $paymentPartiallyRefundedStatus
     */
    public function __construct(string $paymentCapturedStatus, string $paymentErrorStatus, string $paymentPendingStatus, string $paymentAuthorizedStatus, string $paymentCancelledStatus, string $paymentRefundedStatus, string $paymentPartiallyRefundedStatus = '')
    {
        $this->paymentCapturedStatus = $paymentCapturedStatus;
        $this->paymentErrorStatus = $paymentErrorStatus;
        $this->paymentPendingStatus = $paymentPendingStatus;
        $this->paymentAuthorizedStatus = $paymentAuthorizedStatus;
        $this->paymentCancelledStatus = $paymentCancelledStatus;
        $this->paymentRefundedStatus = $paymentRefundedStatus;
        $this->paymentPartiallyRefundedStatus = $paymentPartiallyRefundedStatus;
    }
    /**
     * @return string
     */
    public function getPaymentCapturedStatus(): string
    {
        return $this->paymentCapturedStatus;
    }
    /**
     * @return string
     */
    public function getPaymentErrorStatus(): string
    {
        return $this->paymentErrorStatus;
    }
    /**
     * @return string
     */
    public function getPaymentPendingStatus(): string
    {
        return $this->paymentPendingStatus;
    }
    /**
     * @return string
     */
    public function getPaymentAuthorizedStatus(): string
    {
        return $this->paymentAuthorizedStatus;
    }
    /**
     * @return string
     */
    public function getPaymentCancelledStatus(): string
    {
        return $this->paymentCancelledStatus;
    }
    /**
     * @return string
     */
    public function getPaymentRefundedStatus(): string
    {
        return $this->paymentRefundedStatus;
    }
    /**
     * @return string
     */
    public function getPaymentPartiallyRefundedStatus(): string
    {
        return $this->paymentPartiallyRefundedStatus;
    }
}
