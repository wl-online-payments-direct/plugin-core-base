<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Controller;

use Exception;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request\JobsSettingsRequest;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request\LogSettingsRequest;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request\OrderStatusMappingSettingsRequest;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request\PayByLinkSettingsRequest;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request\PaymentSettingsRequest;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request\WebhookSettingsRequest;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Response\DisconnectResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Response\GeneralSettingsResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Response\SaveSettingsResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Response\SendTestWebhooksResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Disconnect\DisconnectService;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\GeneralSettingsService;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Webhook\TestWebhookService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Exceptions\InvalidConnectionDetailsException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidActionTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidAutomaticCaptureValueException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidLogRecordsLifetimeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPaymentAttemptsNumberException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\NoAdditionalWebhookUrlsException;
/**
 * Class GeneralSettingsController
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Controller
 */
class GeneralSettingsController
{
    protected GeneralSettingsService $generalSettingsService;
    protected DisconnectService $disconnectService;
    protected TestWebhookService $testWebhookService;
    /**
     * @param GeneralSettingsService $generalSettingsService
     * @param DisconnectService $disconnectService
     * @param TestWebhookService $testWebhookService
     */
    public function __construct(GeneralSettingsService $generalSettingsService, DisconnectService $disconnectService, TestWebhookService $testWebhookService)
    {
        $this->generalSettingsService = $generalSettingsService;
        $this->disconnectService = $disconnectService;
        $this->testWebhookService = $testWebhookService;
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
        return new GeneralSettingsResponse($this->generalSettingsService->getGeneralSettings());
    }
    /**
     * @param PaymentSettingsRequest $request
     *
     * @return SaveSettingsResponse
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     * @throws InvalidActionTypeException
     */
    public function savePaymentSettings(PaymentSettingsRequest $request): SaveSettingsResponse
    {
        $this->generalSettingsService->savePaymentSettings($request->transformToDomainModel());
        return new SaveSettingsResponse();
    }
    /**
     * Saves ONLY the Global > Jobs slice (automatic capture). Every other payment setting is left as
     * stored - the merge happens in the service, so this page never has to know, let alone resend,
     * the fields belonging to Danger Zone or Default Settings.
     *
     * @param JobsSettingsRequest $request
     *
     * @return SaveSettingsResponse
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     */
    public function saveJobsSettings(JobsSettingsRequest $request): SaveSettingsResponse
    {
        $this->generalSettingsService->saveJobsSettings($request->transformToDomainModel());
        return new SaveSettingsResponse();
    }
    /**
     * Saves ONLY the Global > Danger Zone slice (the seven order-status mappings). As above, every
     * other payment setting is preserved.
     *
     * @param OrderStatusMappingSettingsRequest $request
     *
     * @return SaveSettingsResponse
     *
     * @throws InvalidPaymentAttemptsNumberException
     */
    public function saveOrderStatusMappingSettings(OrderStatusMappingSettingsRequest $request): SaveSettingsResponse
    {
        $this->generalSettingsService->saveOrderStatusMappingSettings($request->transformToDomainModel());
        return new SaveSettingsResponse();
    }
    /**
     * @param LogSettingsRequest $request
     *
     * @return SaveSettingsResponse
     *
     * @throws InvalidLogRecordsLifetimeException
     */
    public function saveLogSettings(LogSettingsRequest $request): SaveSettingsResponse
    {
        $this->generalSettingsService->saveLogSettings($request->transformToDomainModel());
        return new SaveSettingsResponse();
    }
    /**
     * @param PayByLinkSettingsRequest $request
     *
     * @return SaveSettingsResponse
     */
    public function savePayByLinkSettings(PayByLinkSettingsRequest $request): SaveSettingsResponse
    {
        $this->generalSettingsService->savePayByLinkSettings($request->transformToDomainModel());
        return new SaveSettingsResponse();
    }
    /**
     * @param WebhookSettingsRequest $request
     *
     * @return SaveSettingsResponse
     *
     * @throws \OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidWebhookUrlException
     * @throws \OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\WebhookSettingsNotSupportedException
     */
    public function saveWebhookSettings(WebhookSettingsRequest $request): SaveSettingsResponse
    {
        $this->generalSettingsService->saveWebhookSettings($request->transformToDomainModel());
        return new SaveSettingsResponse();
    }
    /**
     * Standalone "Send Test Webhook" action (WBS S1): sends a mock webhook event to each of the store's
     * configured additional webhook URLs, under the shared health-check retry policy, and reports an
     * outcome per URL. Nothing is persisted.
     *
     * @return SendTestWebhooksResponse
     *
     * @throws InvalidConnectionDetailsException When no connection has been saved for this store yet.
     * @throws NoAdditionalWebhookUrlsException When the store has no additional webhook URLs to test.
     */
    public function sendTestWebhooks(): SendTestWebhooksResponse
    {
        return new SendTestWebhooksResponse($this->testWebhookService->sendTestWebhooks());
    }
    /**
     * @return DisconnectResponse
     *
     * @throws Exception
     */
    public function disconnect(): DisconnectResponse
    {
        $this->disconnectService->disconnect();
        return new DisconnectResponse();
    }
}
