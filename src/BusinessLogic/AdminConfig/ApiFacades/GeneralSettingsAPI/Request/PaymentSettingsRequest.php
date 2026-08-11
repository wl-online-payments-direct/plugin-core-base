<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Request\Request;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Currency;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Exceptions\InvalidCurrencyCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\AutomaticCapture;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\DefaultMethodSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidActionTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidAutomaticCaptureValueException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidExemptionTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPaymentAttemptsNumberException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidSessionTimeoutException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAttemptsNumber;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\SessionTimeout;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ExemptionType;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Request\ConstructsFromArray;
class PaymentSettingsRequest extends Request
{
    use ConstructsFromArray;
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
    protected string $paymentPartiallyRefundedStatus;
    protected bool $sendShoppingCart;
    protected bool $skipConfirmationPage;
    protected int $sessionTimeout;
    protected string $fallbackLocale;
    // Default Settings baseline - the 3DS block is flat here, exactly as `PaymentMethodRequest`
    // carries it, and NULL `enable3ds` means "no store-level 3DS baseline configured".
    protected ?bool $enable3ds;
    protected ?bool $enforceStrongAuthentication;
    protected ?bool $enable3dsExemption;
    protected ?string $exemptionType;
    protected ?float $exemptionLimit;
    protected string $templateIdHostedCheckout;
    protected string $templateIdEmbeddedCheckout;
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
     * @param string $paymentPartiallyRefundedStatus
     * @param bool $sendShoppingCart
     * @param bool $skipConfirmationPage
     * @param int $sessionTimeout Minutes, 1-1440.
     * @param string $fallbackLocale
     * @param bool|null $enable3ds NULL leaves the store without a 3DS baseline.
     * @param bool|null $enforceStrongAuthentication
     * @param bool|null $enable3dsExemption
     * @param string|null $exemptionType
     * @param float|null $exemptionLimit In EUR.
     * @param string $templateIdHostedCheckout
     * @param string $templateIdEmbeddedCheckout
     */
    public function __construct(string $paymentAction, int $automaticCapture, int $paymentAttemptsNumber, bool $applySurcharge, string $paymentCapturedStatus, string $paymentErrorStatus, string $paymentPendingStatus, string $paymentAuthorizedStatus, string $paymentCancelledStatus, string $paymentRefundedStatus, string $paymentPartiallyRefundedStatus = '', bool $sendShoppingCart = \true, bool $skipConfirmationPage = \true, int $sessionTimeout = 180, string $fallbackLocale = 'en_GB', ?bool $enable3ds = null, ?bool $enforceStrongAuthentication = null, ?bool $enable3dsExemption = null, ?string $exemptionType = null, ?float $exemptionLimit = null, string $templateIdHostedCheckout = '', string $templateIdEmbeddedCheckout = '')
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
        $this->paymentPartiallyRefundedStatus = $paymentPartiallyRefundedStatus;
        $this->sendShoppingCart = $sendShoppingCart;
        $this->skipConfirmationPage = $skipConfirmationPage;
        $this->sessionTimeout = $sessionTimeout;
        $this->fallbackLocale = $fallbackLocale;
        $this->enable3ds = $enable3ds;
        $this->enforceStrongAuthentication = $enforceStrongAuthentication;
        $this->enable3dsExemption = $enable3dsExemption;
        $this->exemptionType = $exemptionType;
        $this->exemptionLimit = $exemptionLimit;
        $this->templateIdHostedCheckout = $templateIdHostedCheckout;
        $this->templateIdEmbeddedCheckout = $templateIdEmbeddedCheckout;
    }
    /**
     * @inheritDoc
     *
     * @throws InvalidActionTypeException
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     * @throws InvalidSessionTimeoutException
     */
    public function transformToDomainModel(): object
    {
        return new PaymentSettings(PaymentAction::fromState($this->paymentAction), AutomaticCapture::create($this->automaticCapture), PaymentAttemptsNumber::create($this->paymentAttemptsNumber), $this->applySurcharge, $this->paymentCapturedStatus, $this->paymentErrorStatus, $this->paymentPendingStatus, $this->paymentAuthorizedStatus, $this->paymentCancelledStatus, $this->paymentRefundedStatus, $this->paymentPartiallyRefundedStatus, $this->sendShoppingCart, $this->skipConfirmationPage, SessionTimeout::create($this->sessionTimeout), $this->fallbackLocale, $this->defaultMethodSettings());
    }
    /**
     * @return DefaultMethodSettings
     *
     * @throws InvalidCurrencyCode
     * @throws InvalidExemptionTypeException
     */
    private function defaultMethodSettings(): DefaultMethodSettings
    {
        return new DefaultMethodSettings($this->threeDSSettings(), $this->templateIdHostedCheckout, $this->templateIdEmbeddedCheckout);
    }
    /**
     * Gated on `enable3ds !== null` like `PaymentMethodRequest`, so a page that does not own the
     * Authentication section leaves the store without a 3DS baseline rather than writing one.
     *
     * @return ThreeDSSettings|null
     *
     * @throws InvalidCurrencyCode
     * @throws InvalidExemptionTypeException
     */
    private function threeDSSettings(): ?ThreeDSSettings
    {
        if ($this->enable3ds === null) {
            return null;
        }
        return new ThreeDSSettings(
            $this->enable3ds,
            $this->enforceStrongAuthentication ?? \false,
            $this->enable3dsExemption ?? \false,
            $this->exemptionType ? ExemptionType::fromState($this->exemptionType) : ExemptionType::lowValue(),
            // `!== null`, not truthiness: a limit of 0 means "never exempt anything", and coercing
            // that falsy 0 to null hands it to ThreeDSSettings' EUR 30 default - producing a
            // low-value exemption on every cart under EUR 30 for the merchant who asked for none.
            null !== $this->exemptionLimit ? Amount::fromFloat($this->exemptionLimit, Currency::fromIsoCode('EUR')) : null
        );
    }
}
