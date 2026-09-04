<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories\LogSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories\PayByLinkSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories\PaymentSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories\WebhookSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Repositories\ConnectionConfigRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\AutomaticCapture;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidAutomaticCaptureValueException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidLogRecordsLifetimeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPaymentAttemptsNumberException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\WebhookSettingsNotSupportedException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\GeneralSettingsResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\LogRecordsLifetime;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\LogSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PayByLinkSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAttemptsNumber;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\WebhookSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Stores\StoreService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class GeneralSettingsService
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings
 */
class GeneralSettingsService
{
    protected ConnectionConfigRepositoryInterface $connectionConfigRepository;
    protected LogSettingsRepositoryInterface $logSettingsRepository;
    protected PaymentSettingsRepositoryInterface $paymentSettingsRepository;
    protected StoreService $storeService;
    protected PayByLinkSettingsRepositoryInterface $payByLinkSettingsRepository;
    protected ?WebhookSettingsRepositoryInterface $webhookSettingsRepository;
    /**
     * @param ConnectionConfigRepositoryInterface $connectionConfigRepository
     * @param LogSettingsRepositoryInterface $logSettingsRepository
     * @param PaymentSettingsRepositoryInterface $paymentSettingsRepository
     * @param StoreService $storeService
     * @param PayByLinkSettingsRepositoryInterface $payByLinkSettingsRepository
     * @param WebhookSettingsRepositoryInterface|null $webhookSettingsRepository Only provided for
     *  integrations running with automatic webhooks. With manual webhooks there are no configurable
     *  webhook URLs, so no webhook settings storage is required from the integration.
     */
    public function __construct(ConnectionConfigRepositoryInterface $connectionConfigRepository, LogSettingsRepositoryInterface $logSettingsRepository, PaymentSettingsRepositoryInterface $paymentSettingsRepository, StoreService $storeService, PayByLinkSettingsRepositoryInterface $payByLinkSettingsRepository, ?WebhookSettingsRepositoryInterface $webhookSettingsRepository = null)
    {
        $this->connectionConfigRepository = $connectionConfigRepository;
        $this->logSettingsRepository = $logSettingsRepository;
        $this->paymentSettingsRepository = $paymentSettingsRepository;
        $this->storeService = $storeService;
        $this->payByLinkSettingsRepository = $payByLinkSettingsRepository;
        $this->webhookSettingsRepository = $webhookSettingsRepository;
    }
    /**
     * @return GeneralSettingsResponse
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidLogRecordsLifetimeException
     * @throws InvalidPaymentAttemptsNumberException
     */
    public function getGeneralSettings(): GeneralSettingsResponse
    {
        $connectionSettings = $this->connectionConfigRepository->getConnection();
        $paymentSettings = $this->getPaymentSettings();
        $logSettings = $this->getLogSettings();
        $payByLinkSettings = $this->getPayByLinkSettings();
        $webhookSettings = $this->getWebhookSettings();
        return new GeneralSettingsResponse($connectionSettings, $paymentSettings, $logSettings, $payByLinkSettings, $webhookSettings);
    }
    /**
     * @return PaymentSettings
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     */
    public function getPaymentSettings(): PaymentSettings
    {
        $savedSettings = $this->paymentSettingsRepository->getPaymentSettings();
        if ($savedSettings) {
            return $savedSettings;
        }
        $defaultMapping = $this->storeService->getDefaultOrderStatusMapping();
        return new PaymentSettings(PaymentAction::authorizeCapture(), AutomaticCapture::create(-1), PaymentAttemptsNumber::create(10), \false, $defaultMapping->getPaymentCapturedStatus(), $defaultMapping->getPaymentErrorStatus(), $defaultMapping->getPaymentPendingStatus(), $defaultMapping->getPaymentAuthorizedStatus(), $defaultMapping->getPaymentCancelledStatus(), $defaultMapping->getPaymentRefundedStatus(), '', $defaultMapping->getPaymentPartiallyRefundedStatus());
    }
    /**
     * @param PaymentSettings $paymentSettings
     *
     * @return void
     */
    public function savePaymentSettings(PaymentSettings $paymentSettings): void
    {
        $this->paymentSettingsRepository->savePaymentSettings($paymentSettings);
    }
    /**
     * @return LogSettings
     *
     * @throws InvalidLogRecordsLifetimeException
     */
    public function getLogSettings(): LogSettings
    {
        $savedSettings = $this->logSettingsRepository->getLogSettings();
        return $savedSettings ?: new LogSettings(\false, LogRecordsLifetime::create(14));
    }
    /**
     * @param LogSettings $logSettings
     *
     * @return void
     */
    public function saveLogSettings(LogSettings $logSettings): void
    {
        $this->logSettingsRepository->saveLogSettings($logSettings);
    }
    /**
     * @return PayByLinkSettings
     */
    public function getPayByLinkSettings(): PayByLinkSettings
    {
        $savedSettings = $this->payByLinkSettingsRepository->getPayByLinkSettings();
        return $savedSettings ?: new PayByLinkSettings();
    }
    /**
     * @param PayByLinkSettings $payByLinkSettings
     *
     * @return void
     */
    public function savePayByLinkSettings(PayByLinkSettings $payByLinkSettings): void
    {
        $this->payByLinkSettingsRepository->savePayByLinkSettings($payByLinkSettings);
    }
    /**
     * Returns empty webhook settings when the integration runs with manual webhooks.
     *
     * @return WebhookSettings
     */
    public function getWebhookSettings(): WebhookSettings
    {
        if ($this->webhookSettingsRepository === null) {
            return new WebhookSettings();
        }
        return $this->webhookSettingsRepository->getWebhookSettings() ?: new WebhookSettings();
    }
    /**
     * With manual webhooks there is nowhere to store additional webhook URLs and nothing that would send
     * them, so saving any is refused rather than silently accepted and dropped. Saving an empty set stays
     * a no-op, so an admin UI that still submits the (empty) field does not fail the whole save.
     *
     * @param WebhookSettings $webhookSettings
     *
     * @return void
     *
     * @throws WebhookSettingsNotSupportedException When URLs are given but the integration cannot store them.
     */
    public function saveWebhookSettings(WebhookSettings $webhookSettings): void
    {
        if ($this->webhookSettingsRepository === null) {
            if (!empty($webhookSettings->getAdditionalWebhookUrls())) {
                throw new WebhookSettingsNotSupportedException(new TranslatableLabel('Additional webhook URLs are not supported because this integration uses manual ' . 'webhooks. Register the webhook URL in the Worldline back office instead.', 'generalSettings.webhookSettings.notSupported'));
            }
            return;
        }
        $this->webhookSettingsRepository->saveWebhookSettings($webhookSettings);
    }
}
