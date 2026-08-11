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
        if ($paymentId && !$paymentTransaction->getStatusCode()->isCanceledOrRejected()) {
            return $paymentId;
        }
        $paymentLinkResponse = $this->paymentLinksProxy->getById($paymentLink->getPaymentLinkId(), $merchantReference);
        $linkPaymentId = $paymentLinkResponse->getPaymentLink()->getPaymentId();
        if (!$linkPaymentId) {
            return $paymentId;
        }
        $this->paymentTransactionRepository->updatePaymentId($paymentTransaction, $linkPaymentId);
        return $linkPaymentId;
    }
}
