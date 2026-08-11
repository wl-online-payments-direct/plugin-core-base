<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Request;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Request\Request;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Currency;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Exceptions\InvalidCurrencyCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidActionTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidExemptionTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPayByLinkExpirationTimeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PayByLinkExpirationTime;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidAuthorizationModeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidFlowTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidPaymentProductIdException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidRecurrenceTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidSessionTimeoutException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidSignatureTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\BankTransfer;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Cards\AuthorizationMode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Cards\FlowType;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\CreditCard;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Descriptor;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\GooglePay;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\HostedCheckout;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Intersolve;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Oney;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\PayByLink;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Sepa;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ExemptionType;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethod;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\Translation;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslationCollection;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Request\ConstructsFromArray;
/**
 * Class PaymentMethodRequest
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Request
 */
class PaymentMethodRequest extends Request
{
    use ConstructsFromArray;
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
    protected ?string $authorizationMode;
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
    protected ?string $descriptor;
    // Pay by Link
    protected ?int $expirationTime;
    /** @var string[]|null */
    protected ?array $allowedBrands;
    /** @var array<string, array<string, mixed>>|null */
    protected ?array $brandThreeDSOverrides;
    protected string $fallbackLocale;
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
     * @param string|null $authorizationMode
     * @param string|null $descriptor
     * @param int|null $expirationTime Pay by Link: days a generated link stays valid.
     * @param string[]|null $allowedBrands Card parents: which brands the method offers. Null means
     *     every brand, which is the documented default; an empty array means the merchant cleared
     *     them all.
     * @param array<string, array<string, mixed>>|null $brandThreeDSOverrides Embedded Cards only: per-brand 3DS
     *     overrides, keyed by brand product id, each holding the same five 3DS keys the method level
     *     uses. A brand absent from the map inherits.
     * @param string $fallbackLocale Per-method locale override. EMPTY means inherit the store value.
     */
    public function __construct(string $productId, array $name, bool $enabled, string $template, ?string $paymentAction = null, ?array $vaultTitles = [], ?string $logo = null, ?bool $enableGroupCards = null, ?string $paymentOption = null, ?int $sessionTimeout = null, ?string $intersolveProductId = null, ?string $recurrenceType = null, ?string $signatureType = null, ?bool $instantPayment = null, ?bool $enable3ds = null, ?bool $enforceStrongAuthentication = null, ?bool $enable3dsExemption = null, ?string $exemptionType = null, ?float $amount = null, ?string $flowType = null, ?string $authorizationMode = null, ?string $descriptor = null, ?int $expirationTime = null, ?array $allowedBrands = null, ?array $brandThreeDSOverrides = null, string $fallbackLocale = '')
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
        $this->authorizationMode = $authorizationMode;
        $this->descriptor = $descriptor;
        $this->expirationTime = $expirationTime;
        $this->allowedBrands = $allowedBrands;
        $this->brandThreeDSOverrides = $brandThreeDSOverrides;
        $this->fallbackLocale = $fallbackLocale;
    }
    /**
     * @inheritDoc
     * @return object
     * @throws InvalidCurrencyCode
     * @throws InvalidExemptionTypeException
     * @throws InvalidAuthorizationModeException
     * @throws InvalidFlowTypeException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidRecurrenceTypeException
     * @throws InvalidSessionTimeoutException
     * @throws InvalidSignatureTypeException
     * @throws InvalidActionTypeException
     * @throws InvalidPayByLinkExpirationTimeException
     */
    public function transformToDomainModel(): object
    {
        $productId = PaymentProductId::parse($this->productId);
        $additionalData = null;
        $threeDSSettings = null;
        if (PaymentProductId::bankTransfer()->equals($this->productId)) {
            $additionalData = new BankTransfer($this->instantPayment ?? \false);
        }
        if ($this->enable3ds !== null) {
            $threeDSSettings = new ThreeDSSettings(
                $this->enable3ds,
                $this->enforceStrongAuthentication ?? \false,
                $this->enable3dsExemption ?? \false,
                $this->exemptionType ? ExemptionType::fromState($this->exemptionType) : ExemptionType::lowValue(),
                // `!== null`, not truthiness: a limit of 0 means "never exempt anything", and coercing
                // that falsy 0 to null handed it to ThreeDSSettings' EUR 30 default - producing a
                // low-value exemption on every cart under EUR 30 for the merchant who asked for none.
                null !== $this->amount ? Amount::fromFloat($this->amount, Currency::fromIsoCode('EUR')) : null
            );
        }
        // The grouped `cards` method and every individual card brand share this configuration shape
        // (ADR-0002; functional requirements p11-12).
        if ($productId->hasCreditCardConfiguration()) {
            $additionalData = new CreditCard(
                $this->buildVaultTitles(),
                $threeDSSettings,
                $this->flowType ? FlowType::fromState($this->flowType) : null,
                // Grouping is derived from the method type, not sent by the merchant: both parents are
                // always grouped, an individual brand never is (ADR-0003 decision 12).
                $productId->isCardParent(),
                $this->authorizationMode ? AuthorizationMode::fromState($this->authorizationMode) : null,
                // Only the parents offer a brand selection. Passing it through for a brand method
                // would persist a list nothing reads and let the admin appear to configure it.
                $productId->isCardParent() ? $this->allowedBrands : null,
                // Per-brand overrides are an EMBEDDED CARDS feature only (functional requirements
                // p11 attaches them to that method alone), so Redirection to Cards gets none even
                // though it has an allowed-brands list of its own.
                $productId->equals(PaymentProductId::EMBEDDED_CARDS) ? $this->buildBrandOverrides() : [],
                // `enable3ds === null` is how the admin says "this method inherits" - the same gate
                // that decides whether a block is built at all, carried through so the distinction
                // survives the round trip instead of being re-derived from a materialised block.
                null !== $this->enable3ds
            );
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
        if (PaymentProductId::payByLink()->equals($this->productId)) {
            $additionalData = new PayByLink($this->expirationTime !== null ? PayByLinkExpirationTime::create($this->expirationTime) : null, $this->enableGroupCards ?? \true, $threeDSSettings);
        }
        if ($productId->isDescriptorSupported() && $this->descriptor !== null) {
            $additionalData = new Descriptor($this->descriptor);
        }
        $firstLanguage = array_key_first($this->name);
        $firstName = $this->name[$firstLanguage];
        $nameCollection = new TranslationCollection(new Translation($firstLanguage, $firstName));
        unset($this->name[$firstLanguage]);
        foreach ($this->name as $language => $name) {
            $nameCollection->addTranslation(new Translation($language, $name));
        }
        return new PaymentMethod($productId, $nameCollection, $this->enabled, $this->template, $additionalData, $this->paymentAction ? PaymentAction::fromState($this->paymentAction) : null, 0, $this->fallbackLocale);
    }
    /**
     * The per-brand 3DS overrides, built from the same five keys the method level uses.
     *
     * An entry is only created when `enable3ds` is present, mirroring the method-level gate: sending a
     * brand key with nothing in it means "no override", not "an override that is all defaults". The
     * difference matters because an override made of defaults would pin that brand to
     * `ThreeDSSettings`' lax `enforceStrongAuthentication = false` rather than letting it inherit.
     *
     * @return ThreeDSSettings[]
     *
     * @throws InvalidExemptionTypeException
     * @throws InvalidCurrencyCode
     */
    private function buildBrandOverrides(): array
    {
        $overrides = [];
        foreach ($this->brandThreeDSOverrides ?? [] as $brandId => $settings) {
            if (!is_array($settings) || !isset($settings['enable3ds'])) {
                continue;
            }
            $limit = $settings['exemptionLimit'] ?? $settings['amount'] ?? null;
            $overrides[(string) $brandId] = new ThreeDSSettings(
                (bool) $settings['enable3ds'],
                (bool) ($settings['enforceStrongAuthentication'] ?? \false),
                (bool) ($settings['enable3dsExemption'] ?? \false),
                isset($settings['exemptionType']) ? ExemptionType::fromState($settings['exemptionType']) : ExemptionType::lowValue(),
                // `!== null` rather than truthiness, for the same reason as the method level: a limit
                // of 0 means "never exempt anything", and a falsy 0 coerced to null would hand it
                // ThreeDSSettings' EUR 30 default instead.
                null !== $limit ? Amount::fromFloat((float) $limit, Currency::fromIsoCode('EUR')) : null
            );
        }
        return $overrides;
    }
    /**
     * Vault titles as sent, or null when none were sent. A card brand's configuration form need not
     * carry them, and an absent collection must not be turned into a bogus one.
     */
    private function buildVaultTitles(): ?TranslationCollection
    {
        if (empty($this->vaultTitles)) {
            return null;
        }
        $defaultLanguage = array_key_first($this->vaultTitles);
        $vaultTitles = new TranslationCollection(new Translation($defaultLanguage, $this->vaultTitles[$defaultLanguage]));
        foreach ($this->vaultTitles as $language => $vaultTitle) {
            $vaultTitles->addTranslation(new Translation($language, $vaultTitle));
        }
        return $vaultTitles;
    }
}
