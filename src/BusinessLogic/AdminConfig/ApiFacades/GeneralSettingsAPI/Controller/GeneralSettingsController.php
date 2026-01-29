<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Controller;

use Exception;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request\LogSettingsRequest;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request\PayByLinkSettingsRequest;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request\PaymentSettingsRequest;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Response\DisconnectResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Response\GeneralSettingsResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Response\SaveSettingsResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Disconnect\DisconnectService;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\GeneralSettingsService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidActionTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidAutomaticCaptureValueException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidLogRecordsLifetimeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPaymentAttemptsNumberException;
/**
 * Class GeneralSettingsController
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Controller
 */
class GeneralSettingsController
{
    protected GeneralSettingsService $generalSettingsService;
    protected DisconnectService $disconnectService;
    /**
     * @param GeneralSettingsService $generalSettingsService
     * @param DisconnectService $disconnectService
     */
    public function __construct(GeneralSettingsService $generalSettingsService, DisconnectService $disconnectService)
    {
        $this->generalSettingsService = $generalSettingsService;
        $this->disconnectService = $disconnectService;
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
