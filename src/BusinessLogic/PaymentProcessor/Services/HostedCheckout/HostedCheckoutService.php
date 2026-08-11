<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\HostedCheckout;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\CartProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettingsService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedCheckout\HostedCheckoutSessionRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\PaymentResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Repositories\TokensRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories\PaymentTransactionRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodCollection;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodDefaultConfigs;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\ThreeDSSettingsService;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\HostedCheckoutProxyInterface;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Repositories\ProductTypeRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\PaymentMethod\PaymentMethodService;
/**
 * Class HostedTokenizationService.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\HostedTokenization
 */
class HostedCheckoutService
{
    private HostedCheckoutProxyInterface $hostedCheckoutProxy;
    private PaymentTransactionRepositoryInterface $paymentTransactionRepository;
    private ThreeDSSettingsService $threeDSSettingsService;
    private PaymentSettingsService $paymentSettingsService;
    private TokensRepositoryInterface $tokensRepository;
    private ProductTypeRepositoryInterface $productTypeRepository;
    private PaymentMethodService $paymentMethodService;
    private PaymentProductService $paymentProductService;
    public function __construct(HostedCheckoutProxyInterface $hostedCheckoutProxy, PaymentTransactionRepositoryInterface $paymentTransactionRepository, TokensRepositoryInterface $tokensRepository, ThreeDSSettingsService $threeDSSettingsService, PaymentSettingsService $paymentSettingsService, ProductTypeRepositoryInterface $productTypeRepository, PaymentMethodService $paymentMethodService, PaymentProductService $paymentProductService)
    {
        $this->hostedCheckoutProxy = $hostedCheckoutProxy;
        $this->paymentTransactionRepository = $paymentTransactionRepository;
        $this->tokensRepository = $tokensRepository;
        $this->threeDSSettingsService = $threeDSSettingsService;
        $this->paymentSettingsService = $paymentSettingsService;
        $this->productTypeRepository = $productTypeRepository;
        $this->paymentMethodService = $paymentMethodService;
        $this->paymentProductService = $paymentProductService;
    }
    public function createSession(HostedCheckoutSessionRequest $request): PaymentResponse
    {
        $request = $this->transformForMealvouchers($request);
        $token = null;
        if (null !== $request->getTokenId()) {
            $token = $this->tokensRepository->get($request->getCartProvider()->get()->getCustomer()->getMerchantCustomerId(), $request->getTokenId());
        }
        $offeredMethods = $this->getPaymentMethodsConfig($request->getCartProvider());
        $paymentResponse = $this->hostedCheckoutProxy->createSession($request, $this->resolveThreeDSSettings($request, $offeredMethods), $this->getPaymentSettings(), $offeredMethods, $this->paymentProductService->getSupportedPaymentMethods(), $token);
        if (!$request->getCartProvider()->get()->getCustomer()->isGuest()) {
            $paymentResponse->getPaymentTransaction()->setCustomerId($request->getCartProvider()->get()->getCustomer()->getMerchantCustomerId());
        }
        if ($request->getPaymentProductId()) {
            $paymentResponse->getPaymentTransaction()->setPaymentMethod(array_key_exists($request->getPaymentProductId()->getId(), PaymentMethodDefaultConfigs::PAYMENT_METHOD_CONFIGS) ? PaymentMethodDefaultConfigs::PAYMENT_METHOD_CONFIGS[$request->getPaymentProductId()->getId()]['name']['translation'] : '');
        }
        $this->paymentTransactionRepository->save($paymentResponse->getPaymentTransaction());
        return $paymentResponse;
    }
    public function getThreeDSSettings(PaymentProductId $paymentProductId): ThreeDSSettings
    {
        $savedSettings = $this->threeDSSettingsService->getThreeDSSettings($paymentProductId);
        // `hardened()`, not `new ThreeDSSettings()`: that constructor defaults
        // enforceStrongAuthentication to FALSE, so the old fallback answered "we could not resolve a
        // posture" with a laxer one than any configured method would have produced.
        return $savedSettings ?: ThreeDSSettings::hardened();
    }
    public function getPaymentSettings(): PaymentSettings
    {
        return $this->paymentSettingsService->getPaymentSettings();
    }
    public function getPaymentMethodsConfig(CartProvider $cartProvider): PaymentMethodCollection
    {
        return $this->paymentMethodService->getAvailablePaymentMethods($cartProvider);
    }
    /**
     * Which payment method's configuration may decide how this payment is authenticated.
     *
     * `paymentProductId` is supplied by the caller, and since card brands became payment methods of
     * their own (ADR-0002) it selects a 3DS and exemption configuration. That made an unvalidated
     * input into a security-relevant selector: requesting a brand whose row happens to carry a laxer
     * posture would apply that posture even though the brand is not among the methods offered for this
     * cart.
     *
     * The offered-set test stays HERE rather than moving into the resolver, because this is the only
     * layer that knows it: "offered" means enabled AND available for this cart, which is strictly
     * narrower than the resolver's view of an enabled row. What changed is the answer - an unoffered
     * brand used to fall back to the single grouped `cards` configuration, which no longer exists, and
     * now resolves to the parent that actually allows that brand (ADR-0003 decision 7).
     *
     * @param HostedCheckoutSessionRequest $request
     * @param PaymentMethodCollection $offeredMethods As returned by `getPaymentMethodsConfig()`.
     *
     * @return ThreeDSSettings
     */
    private function resolveThreeDSSettings(HostedCheckoutSessionRequest $request, PaymentMethodCollection $offeredMethods): ThreeDSSettings
    {
        $requested = $request->getPaymentProductId();
        if (null === $requested) {
            return $this->getThreeDSSettings(PaymentProductId::hostedCheckout());
        }
        if ($requested->isCardType() && !$offeredMethods->has($requested)) {
            return $this->threeDSSettingsService->resolveForUnofferedCardRequest($requested)->getSettings();
        }
        return $this->getThreeDSSettings($requested);
    }
    private function transformForMealvouchers(HostedCheckoutSessionRequest $request): HostedCheckoutSessionRequest
    {
        if (null === $request->getPaymentProductId() || !$request->getPaymentProductId()->equals(PaymentProductId::mealvouchers())) {
            return $request;
        }
        return new HostedCheckoutSessionRequest(new MealvouchersCartProvider($this->productTypeRepository, $request->getCartProvider()), $request->getReturnUrl(), $request->getPaymentProductId(), $request->getTokenId());
    }
}
