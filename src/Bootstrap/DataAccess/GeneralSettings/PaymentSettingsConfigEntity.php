<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Exceptions\InvalidCurrencyCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\AutomaticCapture;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\DefaultMethodSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidExemptionTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAttemptsNumber;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\SessionTimeout;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
/**
 * Class PaymentSettingsConfigEntity
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings
 */
class PaymentSettingsConfigEntity extends Entity
{
    public const CLASS_NAME = __CLASS__;
    protected string $storeId;
    protected string $mode;
    protected PaymentSettings $paymentSettings;
    /**
     * @inheritDoc
     */
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('storeId');
        $indexMap->addStringIndex('mode');
        return new EntityConfiguration($indexMap, 'PaymentSettingsEntity');
    }
    public function inflate(array $data): void
    {
        parent::inflate($data);
        $this->storeId = $data['storeId'];
        $this->mode = $data['mode'];
        $paymentSettingsData = $data['paymentSettings'];
        $this->paymentSettings = new PaymentSettings(PaymentAction::fromState($paymentSettingsData['paymentAction']), AutomaticCapture::create($paymentSettingsData['automaticCapture']), $paymentSettingsData['paymentAttemptsNumber'] ? PaymentAttemptsNumber::create($paymentSettingsData['paymentAttemptsNumber']) : null, $paymentSettingsData['applySurcharge'], $paymentSettingsData['paymentCapturedStatus'], $paymentSettingsData['paymentErrorStatus'], $paymentSettingsData['paymentPendingStatus'], $paymentSettingsData['paymentAuthorizedStatus'], $paymentSettingsData['paymentCancelledStatus'], $paymentSettingsData['paymentRefundedStatus'], $paymentSettingsData['paymentPartiallyRefundedStatus'] ?? '', $paymentSettingsData['sendShoppingCart'] ?? \true, $paymentSettingsData['skipConfirmationPage'] ?? \true, isset($paymentSettingsData['sessionTimeout']) ? SessionTimeout::create($paymentSettingsData['sessionTimeout']) : null, $paymentSettingsData['fallbackLocale'] ?? 'en_GB', $this->defaultMethodSettingsFromArray($paymentSettingsData['defaultMethodSettings'] ?? []));
    }
    /**
     * A row written before the Default Settings baseline existed has no `defaultMethodSettings` key,
     * and an absent 3DS block stays NULL rather than becoming a filled-in one: such a store never
     * configured a store-level baseline, and pretending it did would hand the cascade
     * `ThreeDSSettings`' lax constructor defaults instead of its hardened constant.
     *
     * @param array $data
     *
     * @return DefaultMethodSettings
     *
     * @throws InvalidCurrencyCode
     * @throws InvalidExemptionTypeException
     */
    private function defaultMethodSettingsFromArray(array $data): DefaultMethodSettings
    {
        return new DefaultMethodSettings(!empty($data['threeDSSettings']) ? ThreeDSSettings::fromArray($data['threeDSSettings']) : null, $data['templateIdHostedCheckout'] ?? '', $data['templateIdEmbeddedCheckout'] ?? '');
    }
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['storeId'] = $this->storeId;
        $data['mode'] = $this->mode;
        $data['paymentSettings'] = ['paymentAction' => $this->paymentSettings->getPaymentAction()->getType(), 'automaticCapture' => $this->paymentSettings->getAutomaticCapture()->getValue(), 'paymentAttemptsNumber' => $this->paymentSettings->getPaymentAttemptsNumber()->getPaymentAttemptsNumber(), 'applySurcharge' => $this->paymentSettings->isApplySurcharge(), 'paymentCapturedStatus' => $this->paymentSettings->getPaymentCapturedStatus(), 'paymentErrorStatus' => $this->paymentSettings->getPaymentErrorStatus(), 'paymentPendingStatus' => $this->paymentSettings->getPaymentPendingStatus(), 'paymentAuthorizedStatus' => $this->paymentSettings->getPaymentAuthorizedStatus(), 'paymentCancelledStatus' => $this->paymentSettings->getPaymentCancelledStatus(), 'paymentRefundedStatus' => $this->paymentSettings->getPaymentRefundedStatus(), 'paymentPartiallyRefundedStatus' => $this->paymentSettings->getPaymentPartiallyRefundedStatus(), 'sendShoppingCart' => $this->paymentSettings->isSendShoppingCart(), 'skipConfirmationPage' => $this->paymentSettings->isSkipConfirmationPage(), 'sessionTimeout' => $this->paymentSettings->getSessionTimeout()->getMinutes(), 'fallbackLocale' => $this->paymentSettings->getFallbackLocale(), 'defaultMethodSettings' => $this->defaultMethodSettingsToArray()];
        return $data;
    }
    /**
     * @return array
     */
    private function defaultMethodSettingsToArray(): array
    {
        $defaultMethodSettings = $this->paymentSettings->getDefaultMethodSettings();
        $threeDSSettings = $defaultMethodSettings->getThreeDSSettings();
        return ['threeDSSettings' => $threeDSSettings ? $threeDSSettings->toArray() : null, 'templateIdHostedCheckout' => $defaultMethodSettings->getTemplateIdHostedCheckout(), 'templateIdEmbeddedCheckout' => $defaultMethodSettings->getTemplateIdEmbeddedCheckout()];
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
    public function getPaymentSettings(): PaymentSettings
    {
        return $this->paymentSettings;
    }
    public function setPaymentSettings(PaymentSettings $paymentSettings): void
    {
        $this->paymentSettings = $paymentSettings;
    }
}
