<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\PaymentLinks;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories\PaymentTransactionRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentLinks\Repositories\PaymentLinkRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\PaymentLinksProxyInterface;
/**
 * Class PaymentLinkTransactionService
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\PaymentLinks
 */
class PaymentLinkTransactionService
{
    private PaymentLinksProxyInterface $paymentLinksProxy;
    private PaymentLinkRepositoryInterface $paymentLinkRepository;
    private PaymentTransactionRepositoryInterface $paymentTransactionRepository;
    /**
     * @param PaymentLinksProxyInterface $paymentLinksProxy
     * @param PaymentLinkRepositoryInterface $paymentLinkRepository
     * @param PaymentTransactionRepositoryInterface $paymentTransactionRepository
     */
    public function __construct(PaymentLinksProxyInterface $paymentLinksProxy, PaymentLinkRepositoryInterface $paymentLinkRepository, PaymentTransactionRepositoryInterface $paymentTransactionRepository)
    {
        $this->paymentLinksProxy = $paymentLinksProxy;
        $this->paymentLinkRepository = $paymentLinkRepository;
        $this->paymentTransactionRepository = $paymentTransactionRepository;
    }
    public function updatePaymentId(string $merchantReference): ?PaymentId
    {
        $paymentLink = $this->paymentLinkRepository->getByMerchantReference($merchantReference);
        $paymentTransaction = $this->paymentTransactionRepository->getByPaymentLinkId($paymentLink->getPaymentLinkId());
        $paymentId = $paymentTransaction->getPaymentId();
        if ($paymentId) {
            return $paymentId;
        }
        $paymentLinkResponse = $this->paymentLinksProxy->getById($paymentLink->getPaymentLinkId(), $merchantReference);
        $paymentId = $paymentLinkResponse->getPaymentLink()->getPaymentId();
        if (!$paymentId) {
            return null;
        }
        $this->paymentTransactionRepository->updatePaymentId($paymentTransaction, $paymentId);
        return $paymentId;
    }
}
