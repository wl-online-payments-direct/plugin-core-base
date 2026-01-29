<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Request;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Request\Request;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Currency;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Exceptions\InvalidCurrencyCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidActionTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidExemptionTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidFlowTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidPaymentProductIdException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidRecurrenceTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidSessionTimeoutException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidSignatureTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\BankTransfer;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Cards\FlowType;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\CreditCard;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\GooglePay;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\HostedCheckout;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Intersolve;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Oney;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Sepa;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ExemptionType;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethod;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\Translation;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslationCollection;
/**
 * Class PaymentMethodRequest
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Request
 */
class PaymentMethodRequest extends Request
{
    protected string $productId;
    /**
     * @var array<string, string>
     */
    protected array $name;
    protected bool $enabled;
    protected string $template;
    protected ?string $paymentAction;
    // credit card additional data
    /**
     * @var array<string, string> | null
     */
    protected ?array $vaultTitles = [];
    protected ?string $flowType;
    // hosted checkout
    protected ?string $logo;
    protected ?bool $enableGroupCards;
    // Oney
    protected ?string $paymentOption;
    // Intersolve
    protected ?int $sessionTimeout;
    protected ?string $intersolveProductId;
    // Sepa
    protected ?string $recurrenceType;
    protected ?string $signatureType;
    // Bank Transfer
    protected ?bool $instantPayment;
    // 3ds settings
    protected ?bool $enable3ds;
    protected ?bool $enforceStrongAuthentication;
    protected ?bool $enable3dsExemption;
    protected ?string $exemptionType;
    protected ?float $amount;
    /**
     * @param string $productId
     * @param array $name
     * @param bool $enabled
     * @param string $template
     * @param string|null $paymentAction
     * @param string[]|null $vaultTitles
     * @param string|null $logo
     * @param bool|null $enableGroupCards
     * @param string|null $paymentOption
     * @param int|null $sessionTimeout
     * @param string|null $intersolveProductId
     * @param string|null $recurrenceType
     * @param string|null $signatureType
     * @param bool|null $instantPayment
     * @param bool|null $enable3ds
     * @param bool|null $enforceStrongAuthentication
     * @param bool|null $enable3dsExemption
     * @param string|null $exemptionType
     * @param float|null $amount
     * @param string|null $flowType
     */
    public function __construct(string $productId, array $name, bool $enabled, string $template, ?string $paymentAction = null, ?array $vaultTitles = [], ?string $logo = null, ?bool $enableGroupCards = null, ?string $paymentOption = null, ?int $sessionTimeout = null, ?string $intersolveProductId = null, ?string $recurrenceType = null, ?string $signatureType = null, ?bool $instantPayment = null, ?bool $enable3ds = null, ?bool $enforceStrongAuthentication = null, ?bool $enable3dsExemption = null, ?string $exemptionType = null, ?float $amount = null, ?string $flowType = null)
    {
        $this->productId = $productId;
        $this->name = $name;
        $this->enabled = $enabled;
        $this->template = $template;
        $this->paymentAction = $paymentAction;
        $this->vaultTitles = $vaultTitles;
        $this->logo = $logo;
        $this->enableGroupCards = $enableGroupCards;
        $this->paymentOption = $paymentOption;
        $this->sessionTimeout = $sessionTimeout;
        $this->intersolveProductId = $intersolveProductId;
        $this->recurrenceType = $recurrenceType;
        $this->signatureType = $signatureType;
        $this->instantPayment = $instantPayment;
        $this->enable3ds = $enable3ds;
        $this->enforceStrongAuthentication = $enforceStrongAuthentication;
        $this->enable3dsExemption = $enable3dsExemption;
        $this->exemptionType = $exemptionType;
        $this->amount = $amount;
        $this->flowType = $flowType;
    }
    /**
     * @inheritDoc
     * @return object
     * @throws InvalidCurrencyCode
     * @throws InvalidExemptionTypeException
     * @throws InvalidFlowTypeException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidRecurrenceTypeException
     * @throws InvalidSessionTimeoutException
     * @throws InvalidSignatureTypeException
     * @throws InvalidActionTypeException
     */
    public function transformToDomainModel(): object
    {
        $additionalData = null;
        $threeDSSettings = null;
        if (PaymentProductId::bankTransfer()->equals($this->productId)) {
            $additionalData = new BankTransfer($this->instantPayment ?? \false);
        }
        if ($this->enable3ds !== null) {
            $threeDSSettings = new ThreeDSSettings($this->enable3ds, $this->enforceStrongAuthentication ?? \false, $this->enable3dsExemption ?? \false, $this->exemptionType ? ExemptionType::fromState($this->exemptionType) : ExemptionType::lowValue(), $this->amount ? Amount::fromFloat($this->amount, Currency::fromIsoCode('EUR')) : null);
        }
        if (PaymentProductId::cards()->equals($this->productId)) {
            $defaultLanguage = array_key_first($this->vaultTitles);
            $defaultTranslation = $this->vaultTitles[$defaultLanguage];
            $vaultTitles = new TranslationCollection(new Translation($defaultLanguage, $defaultTranslation));
            unset($this->vaultTitles[$defaultLanguage]);
            foreach ($this->vaultTitles as $language => $vaultTitle) {
                $vaultTitles->addTranslation(new Translation($language, $vaultTitle));
            }
            $additionalData = new CreditCard($vaultTitles, $threeDSSettings, $this->flowType ? FlowType::fromState($this->flowType) : null, $this->enableGroupCards ?? \true);
        }
        if (PaymentProductId::hostedCheckout()->equals($this->productId)) {
            $additionalData = new HostedCheckout($this->logo ?? '', $this->enableGroupCards ?? \true, $threeDSSettings);
        }
        if (PaymentProductId::googlePay()->equals($this->productId)) {
            $additionalData = new GooglePay($threeDSSettings);
        }
        if (PaymentProductId::intersolve()->equals($this->productId)) {
            $additionalData = new Intersolve(new Intersolve\SessionTimeout($this->sessionTimeout ?: 180), Intersolve\PaymentProductId::parse($this->intersolveProductId ?: '5700'));
        }
        if (PaymentProductId::oney3x()->equals($this->productId) || PaymentProductId::oney4x()->equals($this->productId) || PaymentProductId::oneyFinancementLong()->equals($this->productId) || PaymentProductId::oneyBrandedGiftCard()->equals($this->productId) || PaymentProductId::oneyBankCard()->equals($this->productId)) {
            $additionalData = new Oney($this->paymentOption ?? '');
        }
        if (PaymentProductId::sepaDirectDebit()->equals($this->productId)) {
            $additionalData = new Sepa($this->recurrenceType ? Sepa\RecurrenceType::parse($this->recurrenceType) : Sepa\RecurrenceType::unique(), isset($this->signatureType) ? Sepa\SignatureType::parse($this->signatureType) : Sepa\SignatureType::sms());
        }
        $firstLanguage = array_key_first($this->name);
        $firstName = $this->name[$firstLanguage];
        $nameCollection = new TranslationCollection(new Translation($firstLanguage, $firstName));
        unset($this->name[$firstLanguage]);
        foreach ($this->name as $language => $name) {
            $nameCollection->addTranslation(new Translation($language, $name));
        }
        return new PaymentMethod(PaymentProductId::parse($this->productId), $nameCollection, $this->enabled, $this->template, $additionalData, $this->paymentAction ? PaymentAction::fromState($this->paymentAction) : null);
    }
}
