<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPaymentAttemptsNumberException;
/**
 * Class PaymentSettings
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings
 */
class PaymentSettings
{
    protected PaymentAction $paymentAction;
    protected AutomaticCapture $automaticCapture;
    protected PaymentAttemptsNumber $paymentAttemptsNumber;
    protected bool $applySurcharge;
    protected string $template;
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
     * Statuses 0, 46, 51, 52, 55.
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
     * @param ?PaymentAction $paymentAction
     * @param ?AutomaticCapture $automaticCapture
     * @param ?PaymentAttemptsNumber $paymentAttemptsNumber
     * @param bool $applySurcharge
     * @param string $paymentCapturedStatus
     * @param string $paymentErrorStatus
     * @param string $paymentPendingStatus
     * @param string $paymentAuthorizedStatus
     * @param string $paymentCancelledStatus
     * @param string $paymentRefundedStatus
     * @param string $template
     * @param string $paymentPartiallyRefundedStatus
     *
     * @throws InvalidPaymentAttemptsNumberException
     */
    public function __construct(?PaymentAction $paymentAction = null, ?AutomaticCapture $automaticCapture = null, ?PaymentAttemptsNumber $paymentAttemptsNumber = null, bool $applySurcharge = \false, string $paymentCapturedStatus = '', string $paymentErrorStatus = '', string $paymentPendingStatus = '', string $paymentAuthorizedStatus = '', string $paymentCancelledStatus = '', string $paymentRefundedStatus = '', string $template = '', string $paymentPartiallyRefundedStatus = '')
    {
        $this->paymentAction = $paymentAction ?? PaymentAction::authorizeCapture();
        $this->automaticCapture = $automaticCapture ?? AutomaticCapture::never();
        $this->paymentAttemptsNumber = $paymentAttemptsNumber ?? PaymentAttemptsNumber::create(10);
        $this->applySurcharge = $applySurcharge;
        $this->paymentCapturedStatus = $paymentCapturedStatus;
        $this->paymentErrorStatus = $paymentErrorStatus;
        $this->paymentPendingStatus = $paymentPendingStatus;
        $this->paymentAuthorizedStatus = $paymentAuthorizedStatus;
        $this->paymentCancelledStatus = $paymentCancelledStatus;
        $this->paymentRefundedStatus = $paymentRefundedStatus;
        $this->template = $template;
        $this->paymentPartiallyRefundedStatus = $paymentPartiallyRefundedStatus;
    }
    /**
     * @return PaymentAction
     */
    public function getPaymentAction(): PaymentAction
    {
        return $this->paymentAction;
    }
    /**
     * @return AutomaticCapture
     */
    public function getAutomaticCapture(): AutomaticCapture
    {
        return $this->automaticCapture;
    }
    /**
     * @return PaymentAttemptsNumber
     */
    public function getPaymentAttemptsNumber(): PaymentAttemptsNumber
    {
        return $this->paymentAttemptsNumber;
    }
    /**
     * @return bool
     */
    public function isApplySurcharge(): bool
    {
        return $this->applySurcharge;
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
    public function getTemplate(): string
    {
        return $this->template;
    }
    /**
     * @return string
     */
    public function getPaymentPartiallyRefundedStatus(): string
    {
        return $this->paymentPartiallyRefundedStatus;
    }
}
