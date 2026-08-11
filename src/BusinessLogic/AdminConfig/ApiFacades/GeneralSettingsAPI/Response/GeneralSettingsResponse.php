<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\ThreeDSSettingsSerializer;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\GeneralSettingsResponse as DomainGeneralSettingsResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Webhook\WebhookMode;
/**
 * Class GeneralSettingsResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Response
 */
class GeneralSettingsResponse extends Response
{
    protected DomainGeneralSettingsResponse $response;
    /**
     * @param DomainGeneralSettingsResponse $response
     */
    public function __construct(DomainGeneralSettingsResponse $response)
    {
        $this->response = $response;
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return ['accountSettings' => $this->connectionSettingsToArray(), 'paymentSettings' => $this->paymentSettingsToArray(), 'logSettings' => $this->logSettingsToArray(), 'payByLinkSettings' => $this->payByLinkSettingsToArray(), 'webhookMode' => WebhookMode::get(), 'webhookSettings' => $this->webhookSettingsToArray()];
    }
    protected function webhookSettingsToArray(): array
    {
        return ['additionalWebhookUrls' => $this->response->getWebhookSettings()->getAdditionalWebhookUrls()];
    }
    protected function connectionSettingsToArray(): array
    {
        return ['mode' => (string) $this->response->getConnectionDetails()->getMode(), 'sandboxData' => ['pspid' => $this->response->getConnectionDetails()->getTestCredentials() ? $this->response->getConnectionDetails()->getTestCredentials()->getPspid() : '', 'apiKey' => $this->response->getConnectionDetails()->getTestCredentials() ? $this->response->getConnectionDetails()->getTestCredentials()->getApiKey() : '', 'apiSecret' => $this->response->getConnectionDetails()->getTestCredentials() ? $this->response->getConnectionDetails()->getTestCredentials()->getApiSecret() : '', 'webhooksKey' => $this->response->getConnectionDetails()->getTestCredentials() ? $this->response->getConnectionDetails()->getTestCredentials()->getWebhookKey() : '', 'webhooksSecret' => $this->response->getConnectionDetails()->getTestCredentials() ? $this->response->getConnectionDetails()->getTestCredentials()->getWebhookSecret() : ''], 'liveData' => ['pspid' => $this->response->getConnectionDetails()->getLiveCredentials() ? $this->response->getConnectionDetails()->getLiveCredentials()->getPspid() : '', 'apiKey' => $this->response->getConnectionDetails()->getLiveCredentials() ? $this->response->getConnectionDetails()->getLiveCredentials()->getApiKey() : '', 'apiSecret' => $this->response->getConnectionDetails()->getLiveCredentials() ? $this->response->getConnectionDetails()->getLiveCredentials()->getApiSecret() : '', 'webhooksKey' => $this->response->getConnectionDetails()->getLiveCredentials() ? $this->response->getConnectionDetails()->getLiveCredentials()->getWebhookKey() : null, 'webhooksSecret' => $this->response->getConnectionDetails()->getLiveCredentials() ? $this->response->getConnectionDetails()->getLiveCredentials()->getWebhookSecret() : null]];
    }
    protected function paymentSettingsToArray(): array
    {
        return array_merge(['paymentAction' => $this->response->getPaymentSettings()->getPaymentAction()->getType(), 'automaticCapture' => $this->response->getPaymentSettings()->getAutomaticCapture()->getValue(), 'numberOfPaymentAttempts' => $this->response->getPaymentSettings()->getPaymentAttemptsNumber()->getPaymentAttemptsNumber(), 'applySurcharge' => $this->response->getPaymentSettings()->isApplySurcharge(), 'paymentCapturedStatus' => $this->response->getPaymentSettings()->getPaymentCapturedStatus(), 'paymentErrorStatus' => $this->response->getPaymentSettings()->getPaymentErrorStatus(), 'paymentPendingStatus' => $this->response->getPaymentSettings()->getPaymentPendingStatus(), 'paymentAuthorizedStatus' => $this->response->getPaymentSettings()->getPaymentAuthorizedStatus(), 'paymentCancelledStatus' => $this->response->getPaymentSettings()->getPaymentCancelledStatus(), 'paymentRefundedStatus' => $this->response->getPaymentSettings()->getPaymentRefundedStatus(), 'paymentPartiallyRefundedStatus' => $this->response->getPaymentSettings()->getPaymentPartiallyRefundedStatus(), 'sendShoppingCart' => $this->response->getPaymentSettings()->isSendShoppingCart(), 'skipConfirmationPage' => $this->response->getPaymentSettings()->isSkipConfirmationPage(), 'sessionTimeout' => $this->response->getPaymentSettings()->getSessionTimeout()->getMinutes(), 'fallbackLocale' => $this->response->getPaymentSettings()->getFallbackLocale()], $this->defaultMethodSettingsToArray());
    }
    /**
     * The store-level Default Settings baseline. `threeDSSettings` is NULL when the merchant has
     * never configured one - which is not the same as a block full of defaults, and is what lets the
     * cascade tell "inherit the baseline" from "there is no baseline" (ADR-0003 decisions 2 and 6).
     *
     * @return array
     */
    protected function defaultMethodSettingsToArray(): array
    {
        $defaultMethodSettings = $this->response->getPaymentSettings()->getDefaultMethodSettings();
        $threeDSSettings = $defaultMethodSettings->getThreeDSSettings();
        return ['threeDSSettings' => $threeDSSettings ? ThreeDSSettingsSerializer::toArray($threeDSSettings) : null, 'templateIdHostedCheckout' => $defaultMethodSettings->getTemplateIdHostedCheckout(), 'templateIdEmbeddedCheckout' => $defaultMethodSettings->getTemplateIdEmbeddedCheckout()];
    }
    protected function logSettingsToArray(): array
    {
        return ['webhookLogging' => $this->response->getLogSettings()->isWebhookLogging(), 'requestResponseLogging' => $this->response->getLogSettings()->isRequestResponseLogging(), 'logDays' => $this->response->getLogSettings()->getLogRecordsLifetime()->getDays()];
    }
    protected function payByLinkSettingsToArray(): array
    {
        return ['enabled' => $this->response->getPayByLinkSettings()->isEnable(), 'title' => $this->response->getPayByLinkSettings()->getTitle(), 'expirationTime' => $this->response->getPayByLinkSettings()->getExpirationTime()->getDays()];
    }
}
