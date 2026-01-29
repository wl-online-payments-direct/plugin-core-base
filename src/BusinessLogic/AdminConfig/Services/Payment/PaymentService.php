<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Payment;

use WOP\OnlinePayments\Core\Branding\Brand\ActiveBrandProviderInterface;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\GeneralSettingsService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidAutomaticCaptureValueException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPaymentAttemptsNumberException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Language\LanguageService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Logo\LogoUrlService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidPaymentProductIdException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidSessionTimeoutException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\BankTransfer;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\CreditCard;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\GooglePay;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\HostedCheckout;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Intersolve;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Oney;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\PaymentMethodAdditionalData;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Sepa;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethod;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodCollection;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodDefaultConfigs;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
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
    public function __construct(PaymentConfigRepositoryInterface $repository, LogoUrlService $logoUrlService, ActiveBrandProviderInterface $activeBrandProvider, PaymentProductService $paymentProductService, GeneralSettingsService $generalSettingsService, LanguageService $languageService)
    {
        $this->repository = $repository;
        $this->logoUrlService = $logoUrlService;
        $this->activeBrandProvider = $activeBrandProvider;
        $this->paymentProductService = $paymentProductService;
        $this->generalSettingsService = $generalSettingsService;
        $this->languageService = $languageService;
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
        $defaultMethods = $this->getSupportedPaymentMethods();
        $configuredMethods = $this->repository->getPaymentMethods();
        return $this->transformToResponse($configuredMethods->union($defaultMethods));
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
        $method = $this->repository->getPaymentMethod($productId);
        if (!$method) {
            $method = $this->getDefaultPaymentMethodConfig((string) $productId);
        }
        $this->repository->savePaymentMethod(new PaymentMethod($method->getProductId(), $method->getName(), $enabled, $method->getTemplate(), $method->getAdditionalData(), $method->getPaymentAction()));
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
            $result[] = new PaymentMethodResponse($paymentMethod->getProductId()->getId(), $paymentMethod->getName(), PaymentMethodDefaultConfigs::getPaymentGroup($paymentMethod->getProductId()->getId()), PaymentMethodDefaultConfigs::getIntegrationTypes($paymentMethod->getProductId()->getId()), $paymentMethod->isEnabled());
        }
        return $result;
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
    protected function getSupportedPaymentProducts(): array
    {
        return $this->paymentProductService->getSupportedPaymentMethods(\false);
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
     */
    protected function getAdditionalData(string $paymentProductId): ?PaymentMethodAdditionalData
    {
        if (PaymentProductId::bankTransfer()->equals($paymentProductId)) {
            return new BankTransfer(\false);
        }
        if (PaymentProductId::cards()->equals($paymentProductId)) {
            return new CreditCard($this->getCreditCardVaultTitles());
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
}
