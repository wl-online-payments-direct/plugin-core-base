<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPaymentAttemptsNumberException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\OrderStatusMapping\Models\OrderStatusMapping;
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
    protected bool $sendShoppingCart;
    protected bool $skipConfirmationPage;
    protected SessionTimeout $sessionTimeout;
    protected string $fallbackLocale;
    protected DefaultMethodSettings $defaultMethodSettings;
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
     * @param string $paymentPartiallyRefundedStatus
     * @param bool $sendShoppingCart
     * @param bool $skipConfirmationPage Bypasses the gateway's own success screen, redirecting the
     *  customer straight back to the store.
     * @param ?SessionTimeout $sessionTimeout How long the hosted payment page session should last.
     * @param string $fallbackLocale Locale used if the store's language is not supported by the gateway.
     * @param ?DefaultMethodSettings $defaultMethodSettings The store-level baseline every payment
     *  method resolves through unless it overrides it (ADR-0003). Deliberately ONE parameter: this
     *  constructor is positional and `php >= 7.4` has no named arguments.
     *
     * @throws InvalidPaymentAttemptsNumberException
     */
    public function __construct(?PaymentAction $paymentAction = null, ?AutomaticCapture $automaticCapture = null, ?PaymentAttemptsNumber $paymentAttemptsNumber = null, bool $applySurcharge = \false, string $paymentCapturedStatus = '', string $paymentErrorStatus = '', string $paymentPendingStatus = '', string $paymentAuthorizedStatus = '', string $paymentCancelledStatus = '', string $paymentRefundedStatus = '', string $paymentPartiallyRefundedStatus = '', bool $sendShoppingCart = \true, bool $skipConfirmationPage = \true, ?SessionTimeout $sessionTimeout = null, string $fallbackLocale = 'en_GB', ?DefaultMethodSettings $defaultMethodSettings = null)
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
        $this->paymentPartiallyRefundedStatus = $paymentPartiallyRefundedStatus;
        $this->sendShoppingCart = $sendShoppingCart;
        $this->skipConfirmationPage = $skipConfirmationPage;
        $this->sessionTimeout = $sessionTimeout ?? SessionTimeout::create(180);
        $this->fallbackLocale = $fallbackLocale;
        $this->defaultMethodSettings = $defaultMethodSettings ?? new DefaultMethodSettings();
    }
    /**
     * Replaces the Global > Jobs slice, leaving every other setting exactly as it is.
     *
     * The savers use these rather than re-enumerating the constructor: a positional re-enumeration
     * that omitted a parameter silently reset that field to its constructor default, so an unrelated
     * page's save could revert the store-wide 3DS baseline with no error.
     *
     * @param AutomaticCapture $automaticCapture
     *
     * @return self
     */
    public function withAutomaticCapture(AutomaticCapture $automaticCapture): self
    {
        $copy = clone $this;
        $copy->automaticCapture = $automaticCapture;
        return $copy;
    }
    /**
     * Replaces the Global > Danger Zone slice - the seven order-status mappings - leaving every other
     * setting exactly as it is. See `withAutomaticCapture()`.
     *
     * @param OrderStatusMapping $mapping
     *
     * @return self
     */
    public function withOrderStatusMapping(OrderStatusMapping $mapping): self
    {
        $copy = clone $this;
        $copy->paymentCapturedStatus = $mapping->getPaymentCapturedStatus();
        $copy->paymentErrorStatus = $mapping->getPaymentErrorStatus();
        $copy->paymentPendingStatus = $mapping->getPaymentPendingStatus();
        $copy->paymentAuthorizedStatus = $mapping->getPaymentAuthorizedStatus();
        $copy->paymentCancelledStatus = $mapping->getPaymentCancelledStatus();
        $copy->paymentRefundedStatus = $mapping->getPaymentRefundedStatus();
        $copy->paymentPartiallyRefundedStatus = $mapping->getPaymentPartiallyRefundedStatus();
        return $copy;
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
    public function getPaymentPartiallyRefundedStatus(): string
    {
        return $this->paymentPartiallyRefundedStatus;
    }
    public function isSendShoppingCart(): bool
    {
        return $this->sendShoppingCart;
    }
    public function isSkipConfirmationPage(): bool
    {
        return $this->skipConfirmationPage;
    }
    public function getSessionTimeout(): SessionTimeout
    {
        return $this->sessionTimeout;
    }
    public function getFallbackLocale(): string
    {
        return $this->fallbackLocale;
    }
    public function getDefaultMethodSettings(): DefaultMethodSettings
    {
        return $this->defaultMethodSettings;
    }
}
