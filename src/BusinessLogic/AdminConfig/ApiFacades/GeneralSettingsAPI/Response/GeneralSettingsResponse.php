<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\GeneralSettingsResponse as DomainGeneralSettingsResponse;
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
        return ['accountSettings' => $this->connectionSettingsToArray(), 'paymentSettings' => $this->paymentSettingsToArray(), 'logSettings' => $this->logSettingsToArray(), 'payByLinkSettings' => $this->payByLinkSettingsToArray()];
    }
    protected function connectionSettingsToArray(): array
    {
        return ['mode' => (string) $this->response->getConnectionDetails()->getMode(), 'sandboxData' => ['pspid' => $this->response->getConnectionDetails()->getTestCredentials() ? $this->response->getConnectionDetails()->getTestCredentials()->getPspid() : '', 'apiKey' => $this->response->getConnectionDetails()->getTestCredentials() ? $this->response->getConnectionDetails()->getTestCredentials()->getApiKey() : '', 'apiSecret' => $this->response->getConnectionDetails()->getTestCredentials() ? $this->response->getConnectionDetails()->getTestCredentials()->getApiSecret() : '', 'webhooksKey' => $this->response->getConnectionDetails()->getTestCredentials() ? $this->response->getConnectionDetails()->getTestCredentials()->getWebhookKey() : '', 'webhooksSecret' => $this->response->getConnectionDetails()->getTestCredentials() ? $this->response->getConnectionDetails()->getTestCredentials()->getWebhookSecret() : ''], 'liveData' => ['pspid' => $this->response->getConnectionDetails()->getLiveCredentials() ? $this->response->getConnectionDetails()->getLiveCredentials()->getPspid() : '', 'apiKey' => $this->response->getConnectionDetails()->getLiveCredentials() ? $this->response->getConnectionDetails()->getLiveCredentials()->getApiKey() : '', 'apiSecret' => $this->response->getConnectionDetails()->getLiveCredentials() ? $this->response->getConnectionDetails()->getLiveCredentials()->getApiSecret() : '', 'webhooksKey' => $this->response->getConnectionDetails()->getLiveCredentials() ? $this->response->getConnectionDetails()->getLiveCredentials()->getWebhookKey() : null, 'webhooksSecret' => $this->response->getConnectionDetails()->getLiveCredentials() ? $this->response->getConnectionDetails()->getLiveCredentials()->getWebhookSecret() : null]];
    }
    protected function paymentSettingsToArray(): array
    {
        return ['paymentAction' => $this->response->getPaymentSettings()->getPaymentAction()->getType(), 'automaticCapture' => $this->response->getPaymentSettings()->getAutomaticCapture()->getValue(), 'numberOfPaymentAttempts' => $this->response->getPaymentSettings()->getPaymentAttemptsNumber()->getPaymentAttemptsNumber(), 'applySurcharge' => $this->response->getPaymentSettings()->isApplySurcharge(), 'paymentCapturedStatus' => $this->response->getPaymentSettings()->getPaymentCapturedStatus(), 'paymentErrorStatus' => $this->response->getPaymentSettings()->getPaymentErrorStatus(), 'paymentPendingStatus' => $this->response->getPaymentSettings()->getPaymentPendingStatus(), 'paymentAuthorizedStatus' => $this->response->getPaymentSettings()->getPaymentAuthorizedStatus(), 'paymentCancelledStatus' => $this->response->getPaymentSettings()->getPaymentCancelledStatus(), 'paymentRefundedStatus' => $this->response->getPaymentSettings()->getPaymentRefundedStatus(), 'template' => $this->response->getPaymentSettings()->getTemplate(), 'paymentPartiallyRefundedStatus' => $this->response->getPaymentSettings()->getPaymentPartiallyRefundedStatus()];
    }
    protected function logSettingsToArray(): array
    {
        return ['debugMode' => $this->response->getLogSettings()->isDebugMode(), 'logDays' => $this->response->getLogSettings()->getLogRecordsLifetime()->getDays()];
    }
    protected function payByLinkSettingsToArray(): array
    {
        return ['enabled' => $this->response->getPayByLinkSettings()->isEnable(), 'title' => $this->response->getPayByLinkSettings()->getTitle(), 'expirationTime' => $this->response->getPayByLinkSettings()->getExpirationTime()->getDays()];
    }
}
