<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Controller;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Request\PaymentMethodRequest;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Response\PaymentMethodEnableResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Response\PaymentMethodResponse as ApiPaymentMethodResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Response\PaymentMethodSaveResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Response\PaymentMethodsResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Payment\PaymentService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Exceptions\InvalidCurrencyCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidActionTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidAutomaticCaptureValueException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidExemptionTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPaymentAttemptsNumberException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Payment\ShopPaymentService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidFlowTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidPaymentProductIdException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidRecurrenceTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidSessionTimeoutException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidSignatureTypeException;
/**
 * Class PaymentController
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\PaymentAPI\Controller
 */
class PaymentController
{
    protected PaymentService $paymentService;
    protected ShopPaymentService $shopPaymentService;
    /**
     * @param PaymentService $paymentService
     * @param ShopPaymentService $shopPaymentService
     */
    public function __construct(PaymentService $paymentService, ShopPaymentService $shopPaymentService)
    {
        $this->paymentService = $paymentService;
        $this->shopPaymentService = $shopPaymentService;
    }
    /**
     * @return PaymentMethodsResponse
     * @throws InvalidPaymentProductIdException
     * @throws InvalidSessionTimeoutException
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     */
    public function list(): PaymentMethodsResponse
    {
        return new PaymentMethodsResponse($this->paymentService->getPaymentMethods());
    }
    /**
     * Statically supported payment methods the merchant currently has enabled in their Worldline
     * account (per a default FR/EUR products query).
     *
     * @return PaymentMethodsResponse
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidSessionTimeoutException
     */
    public function getAvailablePaymentMethods(): PaymentMethodsResponse
    {
        return new PaymentMethodsResponse($this->paymentService->getAvailablePaymentMethods());
    }
    /**
     * Statically supported payment methods the merchant does NOT have enabled in their Worldline
     * account - the complement of getAvailablePaymentMethods() within the supported catalogue, and
     * the "unavailable payment methods" list of the V2 functional requirements (p7).
     *
     * Same response shape as list()/getAvailablePaymentMethods(), so a consumer needs no second
     * mapping. Products the active brand excludes appear in neither list.
     *
     * @return PaymentMethodsResponse
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidSessionTimeoutException
     */
    public function getUnavailablePaymentMethods(): PaymentMethodsResponse
    {
        return new PaymentMethodsResponse($this->paymentService->getUnavailablePaymentMethods());
    }
    /**
     * @param string[] $orderedProductIds Product ids in the desired checkout display order.
     *
     * @return PaymentMethodSaveResponse
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidSessionTimeoutException
     */
    public function reorder(array $orderedProductIds): PaymentMethodSaveResponse
    {
        $this->paymentService->reorderPaymentMethods($orderedProductIds);
        return new PaymentMethodSaveResponse();
    }
    /**
     * @param string $paymentProductId
     * @param bool $enabled
     *
     * @return PaymentMethodEnableResponse
     *
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidSessionTimeoutException
     */
    public function enable(string $paymentProductId, bool $enabled): PaymentMethodEnableResponse
    {
        $this->paymentService->enablePaymentMethod($paymentProductId, $enabled);
        $this->shopPaymentService->enable($paymentProductId, $enabled);
        return new PaymentMethodEnableResponse();
    }
    /**
     * @param PaymentMethodRequest $paymentMethodRequest
     *
     * @return PaymentMethodSaveResponse
     *
     * @throws InvalidPaymentProductIdException
     * @throws InvalidRecurrenceTypeException
     * @throws InvalidSessionTimeoutException
     * @throws InvalidSignatureTypeException
     * @throws InvalidCurrencyCode
     * @throws InvalidActionTypeException
     * @throws InvalidExemptionTypeException
     * @throws InvalidFlowTypeException
     */
    public function save(PaymentMethodRequest $paymentMethodRequest): PaymentMethodSaveResponse
    {
        $method = $paymentMethodRequest->transformToDomainModel();
        $this->paymentService->savePaymentMethod($method);
        $this->shopPaymentService->savePaymentMethod($method);
        return new PaymentMethodSaveResponse();
    }
    /**
     * @param string $paymentProductId
     *
     * @return ApiPaymentMethodResponse
     * @throws InvalidAutomaticCaptureValueException
     * @throws InvalidPaymentAttemptsNumberException
     * @throws InvalidPaymentProductIdException
     * @throws InvalidSessionTimeoutException
     */
    public function getPaymentMethod(string $paymentProductId): ApiPaymentMethodResponse
    {
        return new ApiPaymentMethodResponse(
            $this->paymentService->getPaymentMethod($paymentProductId),
            // The single-method read is the one the configuration modal makes, and it is the only one
            // that renders inherited-vs-set. The listings deliberately do not resolve: it would be an
            // N+1 over ~50 methods on a page that already fans out to the products API.
            $this->paymentService->resolveThreeDSSettings($paymentProductId)
        );
    }
}
