<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\PaymentMethod;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Exceptions\InvalidCurrencyCode;
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
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\PaymentMethodAdditionalData;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Sepa;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ExemptionType;
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
    protected PaymentMethod $paymentMethod;
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('storeId');
        $indexMap->addStringIndex('mode');
        $indexMap->addBooleanIndex('enabled');
        $indexMap->addStringIndex('paymentProductId');
        return new EntityConfiguration($indexMap, 'PaymentMethodConfig');
    }
    public function inflate(array $data): void
    {
        parent::inflate($data);
        $this->storeId = $data['storeId'];
        $this->mode = $data['mode'];
        $this->enabled = $data['enabled'];
        $this->paymentProductId = $data['paymentProductId'];
        $paymentMethod = $data['paymentMethod'] ?? [];
        $firstTranslation = $paymentMethod['nameTranslations'][0];
        $nameTranslations = new TranslationCollection(new Translation($firstTranslation['language'], $firstTranslation['translation']));
        unset($paymentMethod['nameTranslations'][0]);
        foreach ($paymentMethod['nameTranslations'] as $translation) {
            $nameTranslations->addTranslation(new Translation($translation['language'], $translation['translation']));
        }
        $this->paymentMethod = new PaymentMethod(PaymentProductId::parse($paymentMethod['paymentProductId']), $nameTranslations, $paymentMethod['enabled'] ?? \false, $paymentMethod['template'] ?? '', $this->additionalDataFromArray($paymentMethod), !empty($paymentMethod['paymentAction']) ? PaymentAction::fromState($paymentMethod['paymentAction']) : null);
    }
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['storeId'] = $this->storeId;
        $data['mode'] = $this->mode;
        $data['enabled'] = $this->enabled;
        $data['paymentProductId'] = $this->paymentProductId;
        $nameTranslations = [];
        foreach ($this->paymentMethod->getName()->getTranslations() as $item) {
            $nameTranslations[] = ['language' => $item->getLocaleCode(), 'translation' => $item->getMessage()];
        }
        $data['paymentMethod'] = ['paymentProductId' => (string) $this->paymentMethod->getProductId(), 'nameTranslations' => $nameTranslations, 'enabled' => $this->paymentMethod->isEnabled(), 'template' => $this->paymentMethod->getTemplate(), 'paymentAction' => $this->paymentMethod->getPaymentAction() ? $this->paymentMethod->getPaymentAction()->getType() : '', 'additionalData' => $this->additionalDataToArray()];
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
        if (PaymentProductId::cards()->equals($data['paymentProductId'])) {
            $firstTranslation = $additionalData['vaultTitleCollection'][0] ?? null;
            if (empty($firstTranslation)) {
                return null;
            }
            $vaultTitles = new TranslationCollection(new Translation($firstTranslation['languageCode'], $firstTranslation['title']));
            unset($additionalData['vaultTitleCollection'][0]);
            foreach ($additionalData['vaultTitleCollection'] as $vaultTitle) {
                $vaultTitles->addTranslation(new Translation($vaultTitle['languageCode'], $vaultTitle['title']));
            }
            return new CreditCard($vaultTitles, $this->threeDsFromArray($additionalData['threeDSSettings']), $additionalData['flowType'] ? FlowType::fromState($additionalData['flowType']) : null, $additionalData['enableGroupCards'] ?? \false);
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
        return null;
    }
    /**
     * @throws InvalidExemptionTypeException
     * @throws InvalidCurrencyCode
     */
    protected function threeDsFromArray(array $data): ThreeDSSettings
    {
        return new ThreeDSSettings($data['enable3ds'], $data['enforceStrongAuthentication'], $data['enable3dsExemption'], !empty($data['exemptionType']) ? ExemptionType::fromState($data['exemptionType']) : null, !empty($data['exemptionLimit']) ? Amount::fromArray($data['exemptionLimit']) : null);
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
        if ($this->paymentMethod->getProductId()->equals(PaymentProductId::cards()->getId())) {
            $vaultTitles = [];
            foreach ($additionalData->getVaultTitles()->getTranslations() as $vaultTitle) {
                $vaultTitles[] = ['languageCode' => $vaultTitle->getLocaleCode(), 'title' => $vaultTitle->getMessage()];
            }
            return ['vaultTitleCollection' => $vaultTitles, 'threeDSSettings' => $this->threeDsToArray($additionalData->getThreeDSSettings()), 'flowType' => $additionalData->getType()->getType(), 'enableGroupCards' => $additionalData->isEnableGroupCards()];
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
        return [];
    }
    protected function threeDsToArray(ThreeDSSettings $threeDSSettings): array
    {
        return ['enable3ds' => $threeDSSettings->isEnable3ds(), 'enforceStrongAuthentication' => $threeDSSettings->isEnforceStrongAuthentication(), 'enable3dsExemption' => $threeDSSettings->isEnable3dsExemption(), 'exemptionType' => $threeDSSettings->getExemptionType() ? $threeDSSettings->getExemptionType()->getType() : '', 'exemptionLimit' => $threeDSSettings->getExemptionLimit() ? $threeDSSettings->getExemptionLimit()->toArray() : ''];
    }
}
