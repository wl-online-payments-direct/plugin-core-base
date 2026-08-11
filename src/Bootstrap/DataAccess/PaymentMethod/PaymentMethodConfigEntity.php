<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\PaymentMethod;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Exceptions\InvalidCurrencyCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidExemptionTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPayByLinkExpirationTimeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PayByLinkExpirationTime;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
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
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\PaymentMethodAdditionalData;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Sepa;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethod;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\Translation;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslationCollection;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
/**
 * Class PaymentMethodConfigEntity.
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\PaymentMethod
 */
class PaymentMethodConfigEntity extends Entity
{
    public const CLASS_NAME = __CLASS__;
    protected string $storeId;
    protected string $mode;
    protected bool $enabled;
    protected string $paymentProductId;
    protected int $sortOrder;
    protected PaymentMethod $paymentMethod;
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('storeId');
        $indexMap->addStringIndex('mode');
        $indexMap->addBooleanIndex('enabled');
        $indexMap->addStringIndex('paymentProductId');
        $indexMap->addIntegerIndex('sortOrder');
        return new EntityConfiguration($indexMap, 'PaymentMethodConfig');
    }
    public function inflate(array $data): void
    {
        parent::inflate($data);
        $this->storeId = $data['storeId'];
        $this->mode = $data['mode'];
        $this->enabled = $data['enabled'];
        $this->paymentProductId = $data['paymentProductId'];
        $this->sortOrder = $data['sortOrder'] ?? 0;
        $paymentMethod = $data['paymentMethod'] ?? [];
        $firstTranslation = $paymentMethod['nameTranslations'][0];
        $nameTranslations = new TranslationCollection(new Translation($firstTranslation['language'], $firstTranslation['translation']));
        unset($paymentMethod['nameTranslations'][0]);
        foreach ($paymentMethod['nameTranslations'] as $translation) {
            $nameTranslations->addTranslation(new Translation($translation['language'], $translation['translation']));
        }
        $this->paymentMethod = new PaymentMethod(
            PaymentProductId::parse($paymentMethod['paymentProductId']),
            $nameTranslations,
            $paymentMethod['enabled'] ?? \false,
            $paymentMethod['template'] ?? '',
            $this->additionalDataFromArray($paymentMethod),
            !empty($paymentMethod['paymentAction']) ? PaymentAction::fromState($paymentMethod['paymentAction']) : null,
            $paymentMethod['sortOrder'] ?? 0,
            // Optional: empty is exactly what "inherits the store value" means, so an absent key and
            // a blank one are the same state.
            $paymentMethod['fallbackLocale'] ?? ''
        );
    }
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['storeId'] = $this->storeId;
        $data['mode'] = $this->mode;
        $data['enabled'] = $this->enabled;
        $data['paymentProductId'] = $this->paymentProductId;
        $data['sortOrder'] = $this->sortOrder;
        $nameTranslations = [];
        foreach ($this->paymentMethod->getName()->getTranslations() as $item) {
            $nameTranslations[] = ['language' => $item->getLocaleCode(), 'translation' => $item->getMessage()];
        }
        $data['paymentMethod'] = ['paymentProductId' => (string) $this->paymentMethod->getProductId(), 'nameTranslations' => $nameTranslations, 'enabled' => $this->paymentMethod->isEnabled(), 'template' => $this->paymentMethod->getTemplate(), 'paymentAction' => $this->paymentMethod->getPaymentAction() ? $this->paymentMethod->getPaymentAction()->getType() : '', 'additionalData' => $this->additionalDataToArray(), 'sortOrder' => $this->paymentMethod->getSortOrder(), 'fallbackLocale' => $this->paymentMethod->getFallbackLocale()];
        return $data;
    }
    public function getStoreId(): string
    {
        return $this->storeId;
    }
    public function setStoreId(string $storeId): void
    {
        $this->storeId = $storeId;
    }
    public function getMode(): string
    {
        return $this->mode;
    }
    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }
    public function getPaymentProductId(): string
    {
        return $this->paymentProductId;
    }
    public function setPaymentProductId(string $paymentProductId): void
    {
        $this->paymentProductId = $paymentProductId;
    }
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }
    public function setSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }
    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }
    public function setPaymentMethod(PaymentMethod $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }
    /**
     * @param array $data
     *
     * @return PaymentMethodAdditionalData|null
     *
     * @throws InvalidCurrencyCode
     * @throws InvalidExemptionTypeException
     * @throws InvalidFlowTypeException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidRecurrenceTypeException
     * @throws InvalidSessionTimeoutException
     * @throws InvalidSignatureTypeException
     * @throws InvalidPayByLinkExpirationTimeException
     */
    protected function additionalDataFromArray(array $data): ?PaymentMethodAdditionalData
    {
        $additionalData = $data['additionalData'] ?? [];
        if (!$additionalData) {
            return null;
        }
        if (PaymentProductId::bankTransfer()->equals($data['paymentProductId'])) {
            return new BankTransfer($additionalData['instantPayment'] ?? \false);
        }
        // Card brands are payment methods of their own and persist the same shape as `cards`
        // (ADR-0002), so they read back through this one branch.
        if (PaymentProductId::parse($data['paymentProductId'])->hasCreditCardConfiguration()) {
            return new CreditCard(
                $this->vaultTitlesFromArray($additionalData['vaultTitleCollection'] ?? []),
                $this->threeDsFromArray($additionalData['threeDSSettings'] ?? []),
                !empty($additionalData['flowType']) ? FlowType::fromState($additionalData['flowType']) : null,
                $additionalData['enableGroupCards'] ?? \false,
                isset($additionalData['authorizationMode']) ? AuthorizationMode::fromState($additionalData['authorizationMode']) : null,
                // Optional, and null - not [] - is the absent state: CreditCard reads null as every
                // brand, which is what a config that never restricted its brands offers. `?? []` would
                // read the same absence as "no brands at all" and strip the lot.
                $additionalData['allowedBrands'] ?? null,
                $this->brandOverridesFromArray($additionalData['brandThreeDSOverrides'] ?? []),
                // A MISSING key means a row written before the cascade existed, and those rows are
                // treated as SET (ADR-0003 decision 4). Reading absence as "inherits" would have
                // reverted e14bf94's `?? true` hardening for exactly the partial rows it was written
                // for - and worsened it, by making the recovered value merchant-controllable.
                $additionalData['threeDSSettingsSet'] ?? \true
            );
        }
        if (PaymentProductId::hostedCheckout()->equals($data['paymentProductId'])) {
            return new HostedCheckout($additionalData['logo'] ?? '', $additionalData['enableGroupCards'] ?? \false, $this->threeDsFromArray($additionalData['threeDSSettings']));
        }
        if (PaymentProductId::intersolve()->equals($data['paymentProductId'])) {
            return new Intersolve($additionalData['sessionTimeout'] ? new Intersolve\SessionTimeout($additionalData['sessionTimeout']) : null, $additionalData['paymentProductId'] ? Intersolve\PaymentProductId::parse($additionalData['paymentProductId']) : null);
        }
        if (PaymentProductId::oney3x()->equals($data['paymentProductId']) || PaymentProductId::oney4x()->equals($data['paymentProductId']) || PaymentProductId::oneyFinancementLong()->equals($data['paymentProductId']) || PaymentProductId::oneyBrandedGiftCard()->equals($data['paymentProductId']) || PaymentProductId::oneyBankCard()->equals($data['paymentProductId'])) {
            return new Oney($additionalData['paymentOption'] ?? '');
        }
        if (PaymentProductId::sepaDirectDebit()->equals($data['paymentProductId'])) {
            return new Sepa(isset($additionalData['recurrenceType']) ? Sepa\RecurrenceType::parse($additionalData['recurrenceType']) : null, isset($additionalData['signatureType']) ? Sepa\SignatureType::parse($additionalData['signatureType']) : null);
        }
        if (PaymentProductId::googlePay()->equals($data['paymentProductId'])) {
            return new GooglePay($this->threeDsFromArray($additionalData['threeDSSettings']));
        }
        if (PaymentProductId::payByLink()->equals($data['paymentProductId'])) {
            return new PayByLink(isset($additionalData['expirationTime']) ? PayByLinkExpirationTime::create($additionalData['expirationTime']) : null, $additionalData['enableGroupCards'] ?? \false, $this->threeDsFromArray($additionalData['threeDSSettings']));
        }
        if (PaymentProductId::parse($data['paymentProductId'])->isDescriptorSupported()) {
            return new Descriptor($additionalData['descriptor'] ?? '');
        }
        return null;
    }
    /**
     * @param array $rows Rows of ['languageCode' => ..., 'title' => ...].
     *
     * @return TranslationCollection|null Null when nothing was persisted, since a TranslationCollection
     *  always has a default translation and inventing one would fabricate a vault title.
     */
    protected function vaultTitlesFromArray(array $rows): ?TranslationCollection
    {
        $rows = array_values($rows);
        if (empty($rows)) {
            return null;
        }
        $vaultTitles = new TranslationCollection(new Translation($rows[0]['languageCode'], $rows[0]['title']));
        foreach ($rows as $row) {
            $vaultTitles->addTranslation(new Translation($row['languageCode'], $row['title']));
        }
        return $vaultTitles;
    }
    /**
     * @throws InvalidExemptionTypeException
     * @throws InvalidCurrencyCode
     */
    protected function threeDsFromArray(array $data): ThreeDSSettings
    {
        return ThreeDSSettings::fromArray($data);
    }
    /**
     * @return array
     */
    protected function additionalDataToArray(): array
    {
        $additionalData = $this->paymentMethod->getAdditionalData() ?? [];
        if (!$additionalData) {
            return [];
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::bankTransfer()->getId())) {
            return ['instantPayment' => $additionalData->isInstantPayment()];
        }
        if ($this->paymentMethod->getProductId()->hasCreditCardConfiguration()) {
            /** @var CreditCard $additionalData */
            $vaultTitles = [];
            $savedVaultTitles = $additionalData->getVaultTitles();
            foreach ($savedVaultTitles ? $savedVaultTitles->getTranslations() : [] as $vaultTitle) {
                $vaultTitles[] = ['languageCode' => $vaultTitle->getLocaleCode(), 'title' => $vaultTitle->getMessage()];
            }
            return [
                'vaultTitleCollection' => $vaultTitles,
                'threeDSSettings' => $this->threeDsToArray($additionalData->getThreeDSSettings()),
                'flowType' => $additionalData->getType()->getType(),
                'enableGroupCards' => $additionalData->isEnableGroupCards(),
                'authorizationMode' => $additionalData->getAuthorizationMode()->getType(),
                'allowedBrands' => $additionalData->getAllowedBrands(),
                'brandThreeDSOverrides' => $this->brandOverridesToArray($additionalData->getBrandThreeDSOverrides()),
                // Beside the block, never inside it: `src/` has no strict_types, so a sentinel in one
                // of the block's own bool slots would coerce - and "inherit" coerces to TRUE, which on
                // enable3dsExemption means exemptions on (ADR-0003 decision 4).
                'threeDSSettingsSet' => $additionalData->hasThreeDSSettings(),
            ];
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::hostedCheckout()->getId())) {
            return ['logo' => $additionalData->getLogo(), 'enableGroupCards' => $additionalData->isEnableGroupCards(), 'threeDSSettings' => $this->threeDsToArray($additionalData->getThreeDSSettings())];
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::intersolve()->getId())) {
            return ['sessionTimeout' => $additionalData->getSessionTimeout()->getDuration(), 'paymentProductId' => $additionalData->getProductId() ? $additionalData->getProductId()->getId() : null];
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::oney3x()->getId()) || $this->paymentMethod->getProductId()->equals(PaymentProductId::oney4x()->getId()) || $this->paymentMethod->getProductId()->equals(PaymentProductId::oneyBankCard()->getId()) || $this->paymentMethod->getProductId()->equals(PaymentProductId::oneyFinancementLong()->getId()) || $this->paymentMethod->getProductId()->equals(PaymentProductId::oneyBrandedGiftCard()->getId())) {
            return ['paymentOption' => $additionalData->getPaymentOption()];
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::sepaDirectDebit()->getId())) {
            return ['recurrenceType' => $additionalData->getRecurrenceType()->getType(), 'signatureType' => $additionalData->getSignatureType()->getType()];
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::googlePay()->getId())) {
            return ['threeDSSettings' => $this->threeDsToArray($additionalData->getThreeDSSettings())];
        }
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::payByLink()->getId())) {
            /** @var PayByLink $additionalData */
            return ['expirationTime' => $additionalData->getExpirationTime()->getDays(), 'enableGroupCards' => $additionalData->isEnableGroupCards(), 'threeDSSettings' => $this->threeDsToArray($additionalData->getThreeDSSettings())];
        }
        if ($this->paymentMethod->getProductId()->isDescriptorSupported()) {
            return ['descriptor' => $additionalData->getDescriptor()];
        }
        return [];
    }
    protected function threeDsToArray(ThreeDSSettings $threeDSSettings): array
    {
        return $threeDSSettings->toArray();
    }
    /**
     * Per-brand 3DS overrides, keyed by brand product id.
     *
     * Only brands the merchant explicitly overrode are stored - a missing key is what "this brand
     * inherits" means, so writing a full map of every allowed brand would erase the distinction the
     * cascade depends on.
     *
     * @param array<string|int, array> $data
     *
     * @return ThreeDSSettings[]
     */
    protected function brandOverridesFromArray(array $data): array
    {
        $overrides = [];
        foreach ($data as $brandId => $settings) {
            if (is_array($settings) && !empty($settings)) {
                $overrides[(string) $brandId] = $this->threeDsFromArray($settings);
            }
        }
        return $overrides;
    }
    /**
     * @param ThreeDSSettings[] $overrides
     *
     * @return array<string, array>
     */
    protected function brandOverridesToArray(array $overrides): array
    {
        $data = [];
        foreach ($overrides as $brandId => $settings) {
            $data[(string) $brandId] = $this->threeDsToArray($settings);
        }
        return $data;
    }
}
