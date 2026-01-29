<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\Webhooks;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories\PaymentTransactionRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Webhook\WebhookData;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\Payment\StatusUpdateService;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\PaymentLinks\PaymentLinkTransactionService;
class WebhookService
{
    protected StatusUpdateService $statusUpdateService;
    private PaymentTransactionRepositoryInterface $paymentTransactionRepository;
    private PaymentLinkTransactionService $paymentLinkTransactionService;
    private const PAYMENT_LINK_WEBHOOK_TYPE = 'paymentlink.paid';
    private const PAYMENT_CREATED_WEBHOOK_TYPE = 'payment.created';
    /**
     * @param StatusUpdateService $statusUpdateService
     * @param PaymentTransactionRepositoryInterface $paymentTransactionRepository
     * @param PaymentLinkTransactionService $paymentLinkTransactionService
     */
    public function __construct(StatusUpdateService $statusUpdateService, PaymentTransactionRepositoryInterface $paymentTransactionRepository, PaymentLinkTransactionService $paymentLinkTransactionService)
    {
        $this->statusUpdateService = $statusUpdateService;
        $this->paymentTransactionRepository = $paymentTransactionRepository;
        $this->paymentLinkTransactionService = $paymentLinkTransactionService;
    }
    public function process(WebhookData $webhook): void
    {
        $paymentId = PaymentId::parse($webhook->getId());
        if ($webhook->getType() === self::PAYMENT_LINK_WEBHOOK_TYPE) {
            $this->processPaymentLink($webhook, $paymentId);
            return;
        }
        $transaction = $this->paymentTransactionRepository->get($paymentId);
        if ($transaction) {
            $this->statusUpdateService->updateOrderStatus($paymentId);
        }
        // This code handles payment link cancellation.
        // When a payment link is canceled, WL sends a payment.created webhook.
        // Because we do not have payment id for a payment link before this event,
        // we need to retrieve the transaction by merchant reference, update payment id
        // and handle the status update correctly.
        if ($webhook->getType() !== self::PAYMENT_CREATED_WEBHOOK_TYPE) {
            return;
        }
        $transaction = $this->paymentTransactionRepository->getByMerchantReference($webhook->getMerchantReference());
        if (!$transaction) {
            return;
        }
        if (!$transaction->getPaymentId() && $transaction->getPaymentLinkId()) {
            $paymentId = $this->paymentLinkTransactionService->updatePaymentId($webhook->getMerchantReference());
            $this->statusUpdateService->updateOrderStatus($paymentId);
        }
    }
    private function processPaymentLink(WebhookData $webhook, PaymentId $paymentId): void
    {
        $arrayBody = json_decode($webhook->getWebhookBody(), \true);
        if (empty($arrayBody) || !isset($arrayBody['paymentLink'])) {
            return;
        }
        $paymentTransaction = $this->paymentTransactionRepository->getByPaymentLinkId($arrayBody['paymentLink']['paymentLinkId']);
        if (!$paymentTransaction) {
            return;
        }
        $this->paymentTransactionRepository->updatePaymentId($paymentTransaction, $paymentId);
        $this->statusUpdateService->updateOrderStatus($paymentId);
    }
}
