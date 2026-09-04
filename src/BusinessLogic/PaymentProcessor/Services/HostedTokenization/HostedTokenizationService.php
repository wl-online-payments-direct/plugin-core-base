<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\HostedTokenization;

use Exception;
use WOP\OnlinePayments\Core\Branding\Brand\ActiveBrandProviderInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\CartProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\MemoryCachingCartProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Exceptions\TokenDeletionFailureException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Exceptions\TokenNotFoundException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\HostedTokenization;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\PaymentRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\PaymentResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Repositories\TokensRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\TokenResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Logo\LogoUrlService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories\PaymentSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories\PaymentTransactionRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodDefaultConfigs;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\ThreeDSSettingsService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\BackgroundProcesses\WaitPaymentOutcomeProcess;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\HostedTokenizationProxyInterface;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\PaymentsProxyInterface;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\PaymentMethod\PaymentMethodService;
/**
 * Class HostedTokenizationService.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\HostedTokenization
 */
class HostedTokenizationService
{
    private HostedTokenizationProxyInterface $hostedTokenizationProxy;
    private PaymentsProxyInterface $paymentsProxy;
    private PaymentTransactionRepositoryInterface $paymentTransactionRepository;
    private ThreeDSSettingsService $threeDSSettingsService;
    private WaitPaymentOutcomeProcess $waitPaymentOutcomeProcess;
    private PaymentSettingsRepositoryInterface $paymentSettingsRepository;
    private TokensRepositoryInterface $tokensRepository;
    private LogoUrlService $logoUrlService;
    protected ActiveBrandProviderInterface $activeBrandProvider;
    private PaymentMethodService $paymentMethodService;
    public function __construct(HostedTokenizationProxyInterface $hostedTokenizationProxy, PaymentsProxyInterface $paymentsProxy, PaymentTransactionRepositoryInterface $paymentTransactionRepository, ThreeDSSettingsService $threeDSSettingsService, PaymentSettingsRepositoryInterface $paymentSettingsRepository, TokensRepositoryInterface $tokensRepository, WaitPaymentOutcomeProcess $waitPaymentOutcomeProcess, LogoUrlService $logoUrlService, ActiveBrandProviderInterface $activeBrandProvider, PaymentMethodService $paymentMethodService)
    {
        $this->hostedTokenizationProxy = $hostedTokenizationProxy;
        $this->paymentsProxy = $paymentsProxy;
        $this->paymentTransactionRepository = $paymentTransactionRepository;
        $this->threeDSSettingsService = $threeDSSettingsService;
        $this->paymentSettingsRepository = $paymentSettingsRepository;
        $this->tokensRepository = $tokensRepository;
        $this->waitPaymentOutcomeProcess = $waitPaymentOutcomeProcess;
        $this->logoUrlService = $logoUrlService;
        $this->activeBrandProvider = $activeBrandProvider;
        $this->paymentMethodService = $paymentMethodService;
    }
    public function create(CartProvider $cartProvider, ?PaymentProductId $productId = null): HostedTokenization
    {
        return $this->hostedTokenizationProxy->create($cartProvider->get(), [], $productId, $this->paymentMethodService->getCardsTemplate());
    }
    /**
     * Gets valid stored token for a provided cart
     *
     * @param CartProvider $cartProvider
     * @return ?ValidTokensResponse
     */
    public function getValidTokens(CartProvider $cartProvider): ?ValidTokensResponse
    {
        $cartProvider = new MemoryCachingCartProvider($cartProvider);
        if ($cartProvider->get()->getCustomer()->isGuest()) {
            return null;
        }
        $savedTokens = $this->tokensRepository->getForCustomer($cartProvider->get()->getCustomer()->getMerchantCustomerId());
        if (empty($savedTokens)) {
            return null;
        }
        $validTokens = [];
        $invalidTokens = [];
        $hostedTokenization = $this->hostedTokenizationProxy->create($cartProvider->get(), $savedTokens);
        foreach ($savedTokens as $savedToken) {
            if (in_array($savedToken->getTokenId(), $hostedTokenization->getInvalidTokens(), \true)) {
                $invalidTokens[] = $savedToken;
                continue;
            }
            $validTokens[] = $savedToken;
        }
        if (!empty($invalidTokens)) {
            $this->tokensRepository->delete($invalidTokens);
        }
        return new ValidTokensResponse($hostedTokenization, $validTokens);
    }
    public function pay(PaymentRequest $paymentRequest): PaymentResponse
    {
        $token = null;
        if (null !== $paymentRequest->getTokenId()) {
            $token = $this->tokensRepository->get($paymentRequest->getCartProvider()->get()->getCustomer()->getMerchantCustomerId(), $paymentRequest->getTokenId());
        }
        $paymentResponse = $this->paymentsProxy->create($paymentRequest, $this->getThreeDSSettings(), $this->getPaymentSettings(), $token, $this->paymentMethodService->getCardsPaymentAction());
        if (!$paymentRequest->getCartProvider()->get()->getCustomer()->isGuest()) {
            $paymentResponse->getPaymentTransaction()->setCustomerId($paymentRequest->getCartProvider()->get()->getCustomer()->getMerchantCustomerId());
        }
        $this->paymentTransactionRepository->save($paymentResponse->getPaymentTransaction());
        if (null === $paymentResponse->getRedirectUrl()) {
            $this->waitPaymentOutcomeProcess->startInBackground($paymentResponse->getPaymentTransaction()->getPaymentId(), $paymentResponse->getPaymentTransaction()->getReturnHmac());
        }
        return $paymentResponse;
    }
    public function getTokens(string $customerId): array
    {
        $tokens = $this->tokensRepository->getForCustomer($customerId);
        if (empty($tokens)) {
            return [];
        }
        $result = [];
        foreach ($tokens as $token) {
            $result[] = new TokenResponse($token->getTokenId(), PaymentMethodDefaultConfigs::getName($token->getProductId(), $this->activeBrandProvider->getActiveBrand()->getPaymentMethodName())['translation'], $token->getCardNumber(), $token->getExpiryDate(), $this->logoUrlService->getLogoUrl($token->getProductId()));
        }
        return $result;
    }
    /**
     * @param string $customerId
     * @param string $tokenId
     *
     * @return void
     *
     * @throws TokenDeletionFailureException
     * @throws TokenNotFoundException
     */
    public function deleteToken(string $customerId, string $tokenId): void
    {
        $token = $this->tokensRepository->get($customerId, $tokenId);
        if (!$token) {
            throw new TokenNotFoundException(new TranslatableLabel('Token with provided id not found', 'token.notFound'));
        }
        try {
            $this->hostedTokenizationProxy->deleteToken($tokenId);
            $this->tokensRepository->delete([$token]);
        } catch (Exception $e) {
            throw new TokenDeletionFailureException(new TranslatableLabel('Failed to delete token.', 'token.deleteFailure'));
        }
    }
    private function getThreeDSSettings(): ThreeDSSettings
    {
        $savedSettings = $this->threeDSSettingsService->getThreeDSSettings(PaymentProductId::cards());
        return $savedSettings ?: new ThreeDSSettings();
    }
    private function getPaymentSettings(): PaymentSettings
    {
        $savedSettings = $this->paymentSettingsRepository->getPaymentSettings();
        return $savedSettings ?: new PaymentSettings();
    }
}
