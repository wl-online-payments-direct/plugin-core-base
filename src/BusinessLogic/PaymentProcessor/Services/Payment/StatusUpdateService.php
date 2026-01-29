<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\Payment;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories\PaymentSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\AutomaticCapture;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Repositories\TokensRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\ShopOrderService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\ContextLogProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\OrderStatusMapping\StatusMappingService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Exceptions\PaymentTransactionNotFoundException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentDetails;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentTransaction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories\PaymentTransactionRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Time\TimeProviderInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\BackgroundProcesses\WaitPaymentOutcome;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\HostedTokenizationProxyInterface;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\PaymentsProxyInterface;
/**
 * Class StatusUpdateService.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\Payment
 */
class StatusUpdateService
{
    /**
     * Overall max waiting time in seconds for pending transactions. After max wait time exceeds the transaction
     * is no more considered as pending.
     */
    private const MAX_PENDING_TRANSACTIONS_WAIT_TIME = 30;
    private PaymentTransactionRepositoryInterface $paymentTransactionRepository;
    private PaymentsProxyInterface $paymentsProxy;
    private TokensRepositoryInterface $tokensRepository;
    private HostedTokenizationProxyInterface $hostedTokenizationProxy;
    private TimeProviderInterface $timeProvider;
    private ShopOrderService $shopOrderService;
    private StatusMappingService $mappingService;
    private PaymentSettingsRepositoryInterface $paymentSettingsRepository;
    public function __construct(PaymentTransactionRepositoryInterface $paymentTransactionRepository, PaymentsProxyInterface $paymentsProxy, TokensRepositoryInterface $tokensRepository, HostedTokenizationProxyInterface $hostedTokenizationProxy, TimeProviderInterface $timeProvider, ShopOrderService $shopOrderService, StatusMappingService $statusMappingService, PaymentSettingsRepositoryInterface $paymentSettingsRepository)
    {
        $this->paymentTransactionRepository = $paymentTransactionRepository;
        $this->paymentsProxy = $paymentsProxy;
        $this->tokensRepository = $tokensRepository;
        $this->hostedTokenizationProxy = $hostedTokenizationProxy;
        $this->timeProvider = $timeProvider;
        $this->shopOrderService = $shopOrderService;
        $this->mappingService = $statusMappingService;
        $this->paymentSettingsRepository = $paymentSettingsRepository;
    }
    public function updateOrderStatus(PaymentId $paymentId, ?string $returnHmac = null): void
    {
        $paymentTransaction = $this->getPaymentTransaction($paymentId, $returnHmac);
        ContextLogProvider::getInstance()->setPaymentNumber($paymentId->getTransactionId());
        ContextLogProvider::getInstance()->setCurrentOrder($paymentTransaction->getMerchantReference());
        $paymentDetails = $this->paymentsProxy->getPaymentDetails($paymentId);
        $paymentTransaction->setStatusCode($paymentDetails->getStatusCode());
        $paymentTransaction->setPaymentMethod($paymentDetails->getPaymentMethod());
        $this->paymentTransactionRepository->save($paymentTransaction);
        $newState = $this->mappingService->getFullStatusMapping($paymentDetails);
        if (!$newState) {
            return;
        }
        if ($paymentDetails->getStatusCode()->equals(StatusCode::authorized())) {
            $paymentSettings = $this->paymentSettingsRepository->getPaymentSettings();
            if ($paymentSettings && $paymentSettings->getPaymentAction()->equals(PaymentAction::authorize()) && !$paymentSettings->getAutomaticCapture()->equals(AutomaticCapture::never()) && $paymentTransaction->getCaptureAt() === null) {
                $minutes = $paymentSettings->getAutomaticCapture()->getValue();
                $captureTime = $this->timeProvider->getCurrentLocalTime()->add(new \DateInterval("PT{$minutes}M"));
                $paymentTransaction->setCaptureAt($captureTime);
                $this->paymentTransactionRepository->save($paymentTransaction);
            }
        }
        // Refund status must be checked before order create check because partial refunds status code is still 5 or 9
        if ($paymentDetails->getStatusCode()->isRefunded() || $paymentDetails->getAmounts() && $paymentDetails->getAmounts()->getRefundedAmount() && $paymentDetails->getAmounts()->getRefundedAmount()->getPriceInCurrencyUnits() > 0) {
            if ($paymentDetails->getAmounts() && $paymentDetails->getAmounts()->getRefundedAmount()->getValue() > 0 && $paymentDetails->getAmounts()->getRefundedAmount()->getValue() === $paymentDetails->getAmount()->getValue()) {
                $newState = $this->mappingService->getStatusMapping(StatusCode::parse(8));
            }
            $this->shopOrderService->refundShopOrder($paymentTransaction, $paymentDetails, $newState);
            return;
        }
        if ($this->shouldTryOrderCreation($paymentTransaction, $paymentDetails)) {
            $this->createShopOrder($paymentTransaction, $paymentDetails, $newState);
            $this->saveToken($paymentTransaction, $paymentDetails);
            return;
        }
        if ($paymentDetails->getStatusCode()->isCanceledOrRejected()) {
            $this->shopOrderService->cancelShopOrder($paymentTransaction, $paymentDetails, $newState);
            return;
        }
        $this->shopOrderService->updateStatus($paymentTransaction, $paymentDetails, $newState);
    }
    private function getPaymentTransaction(PaymentId $paymentId, ?string $returnHmac = null): PaymentTransaction
    {
        $paymentTransaction = $this->paymentTransactionRepository->get($paymentId, $returnHmac);
        if (!$paymentTransaction) {
            throw new PaymentTransactionNotFoundException(new TranslatableLabel("Payment transaction for payment ID {$paymentId} not found.", 'PaymentProcessor.paymentTransactionNotFound', [(string) $paymentId]));
        }
        return $paymentTransaction;
    }
    private function shouldTryOrderCreation(PaymentTransaction $paymentTransaction, PaymentDetails $paymentDetails): bool
    {
        if ($paymentDetails->getStatusCode()->equals(StatusCode::authorized()) || $paymentDetails->getStatusCode()->equals(StatusCode::completed())) {
            return \true;
        }
        if ($paymentDetails->getStatusCode()->isCanceledOrRejected() || $paymentDetails->getStatusCode()->isRefunded() || $paymentDetails->getStatusCode()->equals(StatusCode::incomplete())) {
            return \false;
        }
        return !$this->getPaymentOutcome($paymentTransaction)->isWaiting();
    }
    public function getPaymentOutcome(PaymentTransaction $paymentTransaction): WaitPaymentOutcome
    {
        return new WaitPaymentOutcome($paymentTransaction, $this->isWaitingTimeExceeded($paymentTransaction));
    }
    private function isWaitingTimeExceeded(PaymentTransaction $paymentTransaction): bool
    {
        $waitStartTime = $paymentTransaction->getReturnedAt();
        if (null === $waitStartTime) {
            // If customer did not return to the shop use transaction creation time adjusted for session expiry period
            $waitStartTime = $paymentTransaction->getCreatedAt()->add(new \DateInterval('PT2H'));
        }
        $currentTime = $this->timeProvider->getCurrentLocalTime();
        return $currentTime->getTimestamp() >= $waitStartTime->getTimestamp() + self::MAX_PENDING_TRANSACTIONS_WAIT_TIME;
    }
    private function createShopOrder(PaymentTransaction $paymentTransaction, PaymentDetails $paymentDetails, string $newState): void
    {
        $locked = $this->paymentTransactionRepository->lockOrderCreation($paymentTransaction->getPaymentId());
        if (!$locked) {
            return;
        }
        try {
            $this->shopOrderService->createShopOrder($paymentTransaction, $paymentDetails, $newState);
        } finally {
            $this->paymentTransactionRepository->unlockOrderCreation($paymentTransaction->getPaymentId());
        }
    }
    private function saveToken(PaymentTransaction $paymentTransaction, PaymentDetails $paymentDetails): void
    {
        if (empty($paymentTransaction->getCustomerId()) || null === $paymentDetails->getTokenId()) {
            return;
        }
        $storedToken = $this->tokensRepository->get($paymentTransaction->getCustomerId(), $paymentDetails->getTokenId());
        if (null !== $storedToken) {
            return;
        }
        $token = $this->hostedTokenizationProxy->getToken($paymentTransaction->getCustomerId(), $paymentDetails->getTokenId());
        if (null !== $token) {
            $this->tokensRepository->save($paymentTransaction->getCustomerId(), $token);
        }
    }
}
