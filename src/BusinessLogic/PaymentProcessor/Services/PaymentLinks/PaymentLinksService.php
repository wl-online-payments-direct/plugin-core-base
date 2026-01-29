<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\PaymentLinks;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories\PayByLinkSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\CartProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PayByLinkSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentTransaction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories\PaymentSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories\PaymentTransactionRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentLinks\PaymentLinkRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentLinks\PaymentLinkResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentLinks\Repositories\PaymentLinkRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodCollection;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\ThreeDSSettingsService;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\PaymentLinksProxyInterface;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\PaymentMethod\PaymentMethodService;
/**
 * Class PaymentLinksService.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\PaymentMethod
 */
class PaymentLinksService
{
    private PaymentLinksProxyInterface $paymentLinksProxy;
    private ThreeDSSettingsService $threeDSSettingsService;
    private PaymentSettingsRepositoryInterface $paymentSettingsRepository;
    private PayByLinkSettingsRepositoryInterface $payByLinkSettingsRepository;
    private PaymentLinkRepositoryInterface $paymentLinkRepository;
    private PaymentTransactionRepositoryInterface $paymentTransactionRepository;
    private PaymentMethodService $paymentMethodService;
    private PaymentProductService $paymentProductService;
    public function __construct(PaymentLinksProxyInterface $paymentLinksProxy, ThreeDSSettingsService $threeDSSettingsService, PaymentSettingsRepositoryInterface $paymentSettingsRepository, PayByLinkSettingsRepositoryInterface $payByLinkSettingsRepository, PaymentLinkRepositoryInterface $paymentLinkRepository, PaymentTransactionRepositoryInterface $paymentTransactionRepository, PaymentMethodService $paymentMethodService, PaymentProductService $paymentProductService)
    {
        $this->paymentLinksProxy = $paymentLinksProxy;
        $this->threeDSSettingsService = $threeDSSettingsService;
        $this->paymentSettingsRepository = $paymentSettingsRepository;
        $this->payByLinkSettingsRepository = $payByLinkSettingsRepository;
        $this->paymentLinkRepository = $paymentLinkRepository;
        $this->paymentTransactionRepository = $paymentTransactionRepository;
        $this->paymentMethodService = $paymentMethodService;
        $this->paymentProductService = $paymentProductService;
    }
    public function create(PaymentLinkRequest $request): PaymentLinkResponse
    {
        $response = $this->paymentLinksProxy->create($request, $this->getThreeDSSettings(), $this->getPaymentSettings(), $this->getPayByLinkSettings(), $this->getPaymentMethods($request->getCartProvider()), $this->paymentProductService->getSupportedPaymentMethods());
        $this->paymentLinkRepository->save($response->getPaymentLink());
        $this->paymentTransactionRepository->save(PaymentTransaction::createFromPaymentLink($response->getPaymentLink()));
        return $response;
    }
    public function get(string $merchantReference): PaymentLinkResponse
    {
        $response = $this->paymentLinkRepository->getByMerchantReference($merchantReference);
        if ($response && (!$response->getExpiresAt() || $response->getExpiresAt() < new \DateTime())) {
            return new PaymentLinkResponse(null);
        }
        return new PaymentLinkResponse($response);
    }
    private function getThreeDSSettings(): ThreeDSSettings
    {
        $savedSettings = $this->threeDSSettingsService->getThreeDSSettings(PaymentProductId::hostedCheckout());
        return $savedSettings ?: new ThreeDSSettings();
    }
    private function getPaymentSettings(): PaymentSettings
    {
        $savedSettings = $this->paymentSettingsRepository->getPaymentSettings();
        return $savedSettings ?: new PaymentSettings();
    }
    private function getPayByLinkSettings(): PayByLinkSettings
    {
        $savedSettings = $this->payByLinkSettingsRepository->getPayByLinkSettings();
        return $savedSettings ?: new PayByLinkSettings();
    }
    private function getPaymentMethods(CartProvider $cartProvider): PaymentMethodCollection
    {
        return $this->paymentMethodService->getAvailablePaymentMethods($cartProvider);
    }
}
