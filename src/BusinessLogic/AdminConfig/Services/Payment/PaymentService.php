<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Payment;

use WOP\OnlinePayments\Core\Branding\Brand\ActiveBrandProviderInterface;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\GeneralSettingsService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidAutomaticCaptureValueException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPayByLinkExpirationTimeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPaymentAttemptsNumberException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Language\LanguageService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Logo\LogoUrlService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidPaymentProductIdException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Cards\FlowType;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidSessionTimeoutException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\BankTransfer;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\CreditCard;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Descriptor;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\GooglePay;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\HostedCheckout;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Intersolve;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Oney;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\PayByLink;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\PaymentMethodAdditionalData;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Sepa;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethod;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodCollection;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodDefaultConfigs;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\ResolvedThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\ThreeDSSettingsService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Repositories\PaymentConfigRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\Translation;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslationCollection;
/**
 * Class PaymentService
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Payment
 */
class PaymentService
{
    public const CREDIT_CARD_VAULT_TITLES = ['EN' => 'Saved credit card', 'DE' => 'Kreditkarte gespeichert', 'FR' => 'Carte de crédit enregistrée', 'ES' => 'Tarjeta de crédito guardada', 'IT' => 'Carta di credito salvata'];
    protected PaymentConfigRepositoryInterface $repository;
    protected LogoUrlService $logoUrlService;
    protected ThreeDSSettingsService $threeDSSettingsService;
    protected ActiveBrandProviderInterface $activeBrandProvider;
    protected PaymentProductService $paymentProductService;
    protected GeneralSettingsService $generalSettingsService;
    protected LanguageService $languageService;
    /**
     * @param PaymentConfigRepositoryInterface $repository
     * @param LogoUrlService $logoUrlService
     * @param ActiveBrandProviderInterface $activeBrandProvider
     * @param PaymentProductService $paymentProductService
     * @param GeneralSettingsService $generalSettingsService
     * @param LanguageService $languageService
     */
    public function __construct(PaymentConfigRepositoryInterface $repository, LogoUrlService $logoUrlService, ActiveBrandProviderInterface $activeBrandProvider, PaymentProductService $paymentProductService, GeneralSettingsService $generalSettingsService, LanguageService $languageService, ThreeDSSettingsService $threeDSSettingsService)
    {
        $this->repository = $repository;
        $this->logoUrlService = $logoUrlService;
        $this->activeBrandProvider = $activeBrandProvider;
        $this->paymentProductService = $paymentProductService;
        $this->generalSettingsService = $generalSettingsService;
        $this->languageService = $languageService;
        $this->threeDSSettingsService = $threeDSSettingsService;
    }
    /**
     * Retrieves payment methods configurations.
     *
     * @return PaymentMethodResponse[]
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidSessionTimeoutException
     */
    public function getPaymentMethods(): array
    {
        return $this->transformToResponse(new PaymentMethodCollection($this->withoutBrandExclusions($this->getAllConfiguredMethods()->toArray())));
    }
    /**
     * Statically supported payment methods that Worldline currently reports as available for this
     * merchant account, per PaymentProductService::getAvailableProductIds() (a default FR/EUR query,
     * since admin config has no cart to derive country/currency from). Each entry still carries its
     * saved configuration if one exists.
     *
     * @return PaymentMethodResponse[]
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidSessionTimeoutException
     */
    public function getAvailablePaymentMethods(): array
    {
        $availableProductIds = $this->paymentProductService->getAvailableProductIds();
        $availableMethods = array_filter($this->getAllConfiguredMethods()->toArray(), static function (PaymentMethod $paymentMethod) use ($availableProductIds): bool {
            return in_array($paymentMethod->getProductId()->getId(), $availableProductIds, \true);
        });
        return $this->transformToResponse(new PaymentMethodCollection($this->withoutBrandExclusions($availableMethods)));
    }
    /**
     * Statically supported payment methods the merchant's account does NOT currently offer, i.e.
     * everything in the full catalogue that `getAvailablePaymentMethods()` filtered out. This is the
     * "unavailable payment methods" list of the V2 functional requirements (p7): "payment methods
     * that our plugin supports, but the merchant doesn't have them enabled in their account".
     *
     * Set difference, not a second query - the same source collection feeds both sides, so a product
     * can never appear in both lists, and one cannot silently drift from the other.
     *
     * Brand exclusions apply here too, and that is the whole point of them: p7 requires that a
     * product a brand does not offer "should not even appear in the 'unavailable' list".
     *
     * @return PaymentMethodResponse[]
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidSessionTimeoutException
     */
    public function getUnavailablePaymentMethods(): array
    {
        $availableProductIds = $this->paymentProductService->getAvailableProductIds();
        $unavailableMethods = array_filter($this->getAllConfiguredMethods()->toArray(), static function (PaymentMethod $paymentMethod) use ($availableProductIds): bool {
            return !in_array($paymentMethod->getProductId()->getId(), $availableProductIds, \true);
        });
        return $this->transformToResponse(new PaymentMethodCollection($this->withoutBrandExclusions($unavailableMethods)));
    }
    /**
     * Drops the products the active brand must never offer (`BrandConfig::getExcludedPaymentProducts`,
     * declared in the brand's build-time config file).
     *
     * Applied to every admin listing rather than only the unavailable one: a product the brand does
     * not sell has no business appearing anywhere in the configuration UI, and filtering in one
     * listing but not another is how the two lists would disagree. With no exclusions declared - the
     * case for every brand today - this returns its input untouched, so existing behaviour and the
     * suite's product-count assertions are unaffected.
     *
     * @param PaymentMethod[] $paymentMethods
     *
     * @return PaymentMethod[]
     */
    private function withoutBrandExclusions(array $paymentMethods): array
    {
        $excluded = $this->activeBrandProvider->getActiveBrand()->getExcludedPaymentProducts();
        if (empty($excluded)) {
            return $paymentMethods;
        }
        return array_filter($paymentMethods, static function (PaymentMethod $paymentMethod) use ($excluded): bool {
            return !in_array($paymentMethod->getProductId()->getId(), $excluded, \true);
        });
    }
    /**
     * @return PaymentMethodCollection
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidSessionTimeoutException
     */
    private function getAllConfiguredMethods(): PaymentMethodCollection
    {
        $defaultMethods = $this->getSupportedPaymentMethods();
        $configuredMethods = $this->repository->getPaymentMethods();
        return $configuredMethods->union($defaultMethods);
    }
    /**
     * Saves payment method configuration.
     *
     * @param PaymentMethod $paymentMethod
     *
     * @return void
     */
    public function savePaymentMethod(PaymentMethod $paymentMethod): void
    {
        if (PaymentProductId::hostedCheckout()->equals($paymentMethod->getProductId()) && empty($paymentMethod->getAdditionalData()->getLogo())) {
            $paymentMethod->getAdditionalData()->setLogo($this->logoUrlService->getHostedCheckoutLogoUrl());
        }
        $enabledLanguages = $this->languageService->getEnabledLanguages();
        $translations = $paymentMethod->getName()->getTranslations();
        foreach ($enabledLanguages as $language) {
            if (!isset($translations[$language->getCode()])) {
                $paymentMethod->getName()->addTranslation(new Translation($language->getCode(), PaymentMethodDefaultConfigs::getName($paymentMethod->getProductId()->getId(), $this->activeBrandProvider->getActiveBrand()->getPaymentMethodName())['translation']));
            }
        }
        $this->repository->savePaymentMethod($paymentMethod);
    }
    /**
     * @param string $productId
     *
     * @return PaymentMethod|null
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidSessionTimeoutException
     */
    public function getPaymentMethod(string $productId): ?PaymentMethod
    {
        $method = $this->repository->getPaymentMethod($productId);
        if (!$method) {
            $method = $this->getDefaultPaymentMethodConfig($productId);
        }
        return $method;
    }
    /**
     * Rejects a product id outside the catalogue before anything is written under it.
     *
     * Reached from `enablePaymentMethod()`, which takes the id as a RAW STRING off the request: without
     * this, an unknown id would fall through to `getDefaultPaymentMethodConfig()` and materialise a
     * config row for a product the catalogue does not offer. The `savePaymentMethod()` path needs no
     * such guard - its argument is already a `PaymentProductId`, which cannot hold an id outside the
     * catalogue.
     *
     * @throws InvalidPaymentProductIdException
     */
    private function assertConfigurable(string $productId): void
    {
        if (PaymentProductId::isSupported($productId)) {
            return;
        }
        throw new InvalidPaymentProductIdException(new TranslatableLabel(sprintf('Payment product %s cannot be configured.', $productId), 'paymentMethod.notConfigurable', [$productId]));
    }
    /**
     * @param string $productId
     * @param bool $enabled
     *
     * @return void
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidSessionTimeoutException
     */
    public function enablePaymentMethod(string $productId, bool $enabled): void
    {
        $this->assertConfigurable($productId);
        $method = $this->repository->getPaymentMethod($productId);
        if (!$method) {
            $method = $this->getDefaultPaymentMethodConfig((string) $productId);
        }
        $this->repository->savePaymentMethod(new PaymentMethod(
            $method->getProductId(),
            $method->getName(),
            $enabled,
            $method->getTemplate(),
            $method->getAdditionalData(),
            $method->getPaymentAction(),
            // Carried explicitly. This is a read-modify-write over a positional constructor, so
            // every field the caller forgets is silently reset - and this one used to forget
            // sortOrder, which meant toggling a method jumped it to the front of the merchant's
            // configured checkout order.
            $method->getSortOrder(),
            $method->getFallbackLocale()
        ));
    }
    /**
     * Persists the merchant's chosen checkout display order for payment methods. Product ids not
     * previously configured get a default config created for them (same as enablePaymentMethod()),
     * so they too keep their position from now on.
     *
     * @param string[] $orderedProductIds Product ids in the desired display order.
     *
     * @return void
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidSessionTimeoutException
     */
    public function reorderPaymentMethods(array $orderedProductIds): void
    {
        foreach (array_values($orderedProductIds) as $sortOrder => $productId) {
            $method = $this->getPaymentMethod($productId);
            $this->repository->savePaymentMethod(new PaymentMethod($method->getProductId(), $method->getName(), $method->isEnabled(), $method->getTemplate(), $method->getAdditionalData(), $method->getPaymentAction(), $sortOrder, $method->getFallbackLocale()));
        }
    }
    /**
     * @param PaymentMethodCollection $collection
     *
     * @return PaymentMethodResponse[]
     */
    protected function transformToResponse(PaymentMethodCollection $collection): array
    {
        $result = [];
        foreach ($collection->toArray() as $paymentMethod) {
            $result[] = new PaymentMethodResponse($paymentMethod->getProductId()->getId(), $paymentMethod->getName(), PaymentMethodDefaultConfigs::getPaymentGroup($paymentMethod->getProductId()->getId()), PaymentMethodDefaultConfigs::getIntegrationTypes($paymentMethod->getProductId()->getId()), $paymentMethod->isEnabled(), $this->configuredFlowType($paymentMethod));
        }
        return $result;
    }
    /**
     * The flow the merchant configured for a method, for the methods where that is a setting.
     *
     * Only card methods have one - it is the "Type" field of their config - so everything else
     * returns null and the admin list falls back to labelling that method by its kind. Deliberately
     * NOT derived from the catalogue's integration types: a card brand supports several flows and is
     * configured as exactly one, and those are different questions with different answers.
     *
     * @param PaymentMethod $paymentMethod
     *
     * @return string|null
     */
    protected function configuredFlowType(PaymentMethod $paymentMethod): ?string
    {
        $additionalData = $paymentMethod->getAdditionalData();
        if (!$additionalData instanceof CreditCard) {
            return null;
        }
        return $additionalData->getType()->getType();
    }
    /**
     * @return PaymentMethodCollection
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidSessionTimeoutException
     */
    protected function getSupportedPaymentMethods(): PaymentMethodCollection
    {
        $methods = [];
        foreach ($this->getSupportedPaymentProducts() as $paymentProductId) {
            $methods[] = $this->getDefaultPaymentMethodConfig($paymentProductId);
        }
        return new PaymentMethodCollection($methods);
    }
    /**
     * The catalogue the listing is synthesised from. It includes the card brands, which is what makes
     * every brand appear as a configurable row even for a store upgrading from V1 that has no
     * per-brand configuration persisted yet (ADR-0002).
     *
     * @return string[]
     */
    protected function getSupportedPaymentProducts(): array
    {
        return $this->paymentProductService->getSupportedPaymentMethods();
    }
    /**
     * @param string $paymentProductId
     *
     * @return PaymentMethod
     *
     * @throws InvalidPaymentProductIdException
     * @throws InvalidSessionTimeoutException
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     */
    protected function getDefaultPaymentMethodConfig(string $paymentProductId): PaymentMethod
    {
        $defaultName = PaymentMethodDefaultConfigs::getName($paymentProductId, $this->activeBrandProvider->getActiveBrand()->getPaymentMethodName());
        $name = new Translation($defaultName['language'], $defaultName['translation']);
        $paymentProductId = PaymentProductId::parse($paymentProductId);
        $paymentAction = null;
        if ($paymentProductId->isSeparateCaptureSupported()) {
            $paymentSettings = $this->generalSettingsService->getPaymentSettings();
            $paymentAction = $paymentSettings->getPaymentAction();
        }
        return new PaymentMethod($paymentProductId, new TranslationCollection($name), \false, '', $this->getAdditionalData($paymentProductId), $paymentAction);
    }
    /**
     * @throws InvalidSessionTimeoutException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidPayByLinkExpirationTimeException
     */
    protected function getAdditionalData(string $paymentProductId): ?PaymentMethodAdditionalData
    {
        $productId = PaymentProductId::parse($paymentProductId);
        if (PaymentProductId::bankTransfer()->equals($paymentProductId)) {
            return new BankTransfer(\false);
        }
        if ($productId->isCardParent()) {
            // Both parents are always grouped and their flow type is fixed by which method they are
            // (functional requirements p11), so neither is stored as a merchant choice. The flow type
            // is still seeded to match, because CreditCard has one and Redirection to Cards must not
            // carry the iframe default.
            return new CreditCard($this->getCreditCardVaultTitles(), null, $productId->equals(PaymentProductId::REDIRECTION_TO_CARDS) ? FlowType::redirect() : FlowType::iframe(), \true);
        }
        if ($productId->isCardBrand()) {
            // Functional requirements p12: an individual card has the credit-card configuration
            // "with the following modifications: Remove the Enable group cards - it is always
            // considered as ungrouped". Grouping belongs to the two parents alone, so the flag is
            // pinned to false here rather than inheriting CreditCard's grouped default.
            return new CreditCard($this->getCreditCardVaultTitles(), null, null, \false);
        }
        if (PaymentProductId::googlePay()->equals($paymentProductId)) {
            return new GooglePay();
        }
        if (PaymentProductId::hostedCheckout()->equals($paymentProductId)) {
            return new HostedCheckout($this->logoUrlService->getHostedCheckoutLogoUrl(), \true);
        }
        if (PaymentProductId::intersolve()->equals($paymentProductId)) {
            return new Intersolve(new Intersolve\SessionTimeout(180), Intersolve\PaymentProductId::parse('5700'));
        }
        if (PaymentProductId::oney3x()->equals($paymentProductId) || PaymentProductId::oney4x()->equals($paymentProductId) || PaymentProductId::oneyFinancementLong()->equals($paymentProductId) || PaymentProductId::oneyBrandedGiftCard()->equals($paymentProductId) || PaymentProductId::oneyBankCard()->equals($paymentProductId)) {
            return new Oney('');
        }
        if (PaymentProductId::sepaDirectDebit()->equals($paymentProductId)) {
            return new Sepa(Sepa\RecurrenceType::unique(), Sepa\SignatureType::sms());
        }
        if (PaymentProductId::payByLink()->equals($paymentProductId)) {
            return new PayByLink();
        }
        if ($productId->isDescriptorSupported()) {
            return new Descriptor('');
        }
        return null;
    }
    protected function getCreditCardVaultTitles(): TranslationCollection
    {
        $default = new Translation('EN', self::CREDIT_CARD_VAULT_TITLES['EN']);
        $vaultTitleCollection = new TranslationCollection($default);
        foreach (self::CREDIT_CARD_VAULT_TITLES as $lang => $title) {
            $vaultTitleCollection->addTranslation(new Translation($lang, $title));
        }
        return $vaultTitleCollection;
    }
    /**
     * What this method's 3DS block resolves to today, and which level answered.
     *
     * The admin needs BOTH this and the stored block. The modal greys out inherited fields, and it
     * cannot derive that by comparing the stored values with the baseline: equality is not identity - a
     * method explicitly set to the same values as the store default is not inheriting, and would
     * wrongly grey out and then change the day the default moved.
     *
     * @return ResolvedThreeDSSettings|null Null for methods that carry no 3DS configuration at all.
     *
     * @throws InvalidPaymentProductIdException
     */
    public function resolveThreeDSSettings(string $paymentProductId): ?ResolvedThreeDSSettings
    {
        return $this->threeDSSettingsService->resolveForMethod(PaymentProductId::parse($paymentProductId));
    }
}
