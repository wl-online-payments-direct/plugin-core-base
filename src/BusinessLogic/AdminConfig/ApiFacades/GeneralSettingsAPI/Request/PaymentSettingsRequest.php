<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Request\Request;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\AutomaticCapture;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidActionTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidAutomaticCaptureValueException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPaymentAttemptsNumberException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAttemptsNumber;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
class PaymentSettingsRequest extends Request
{
    protected string $paymentAction;
    protected int $automaticCapture;
    protected int $paymentAttemptsNumber;
    protected bool $applySurcharge;
    protected string $paymentCapturedStatus;
    protected string $paymentErrorStatus;
    protected string $paymentPendingStatus;
    protected string $paymentAuthorizedStatus;
    protected string $paymentCancelledStatus;
    protected string $paymentRefundedStatus;
    protected string $template;
    protected string $paymentPartiallyRefundedStatus;
    /**
     * @param string $paymentAction
     * @param int $automaticCapture
     * @param int $paymentAttemptsNumber
     * @param bool $applySurcharge
     * @param string $paymentCapturedStatus
     * @param string $paymentErrorStatus
     * @param string $paymentPendingStatus
     * @param string $paymentAuthorizedStatus
     * @param string $paymentCancelledStatus
     * @param string $paymentRefundedStatus
     * @param string $template
     * @param string $paymentPartiallyRefundedStatus
     */
    public function __construct(string $paymentAction, int $automaticCapture, int $paymentAttemptsNumber, bool $applySurcharge, string $paymentCapturedStatus, string $paymentErrorStatus, string $paymentPendingStatus, string $paymentAuthorizedStatus, string $paymentCancelledStatus, string $paymentRefundedStatus, string $template, string $paymentPartiallyRefundedStatus = '')
    {
        $this->paymentAction = $paymentAction;
        $this->automaticCapture = $automaticCapture;
        $this->paymentAttemptsNumber = $paymentAttemptsNumber;
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
     * @inheritDoc
     *
     * @throws InvalidActionTypeException
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     */
    public function transformToDomainModel(): object
    {
        return new PaymentSettings(PaymentAction::fromState($this->paymentAction), AutomaticCapture::create($this->automaticCapture), PaymentAttemptsNumber::create($this->paymentAttemptsNumber), $this->applySurcharge, $this->paymentCapturedStatus, $this->paymentErrorStatus, $this->paymentPendingStatus, $this->paymentAuthorizedStatus, $this->paymentCancelledStatus, $this->paymentRefundedStatus, $this->template, $this->paymentPartiallyRefundedStatus);
    }
}
