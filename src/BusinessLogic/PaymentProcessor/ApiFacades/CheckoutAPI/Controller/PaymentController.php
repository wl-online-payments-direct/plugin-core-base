<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Controller;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories\PaymentTransactionRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Response\PaymentOutcomeResponse;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Response\PaymentTransactionResponse;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Response\UpdateStatusResponse;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\BackgroundProcesses\WaitPaymentOutcomeProcess;
/**
 * Class PaymentController.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Controller
 */
class PaymentController
{
    private WaitPaymentOutcomeProcess $waitPaymentOutcomeProcess;
    private PaymentTransactionRepositoryInterface $paymentTransactionRepository;
    public function __construct(WaitPaymentOutcomeProcess $waitPaymentOutcomeProcess, PaymentTransactionRepositoryInterface $paymentTransactionRepository)
    {
        $this->waitPaymentOutcomeProcess = $waitPaymentOutcomeProcess;
        $this->paymentTransactionRepository = $paymentTransactionRepository;
    }
    public function startWaitingForOutcome(?PaymentId $paymentId, ?string $returnHmac = null, ?string $merchantReference = null): void
    {
        StoreContext::getInstance()->setOrigin('landing');
        if (!$paymentId && $merchantReference) {
            $this->waitPaymentOutcomeProcess->startWaitingForPaymentLink($merchantReference);
            return;
        }
        $this->waitPaymentOutcomeProcess->startWaiting($paymentId, $returnHmac);
    }
    public function startWaitingForOutcomeInBackground(?PaymentId $paymentId, ?string $returnHmac = null, ?string $merchantReference = null): void
    {
        StoreContext::getInstance()->setOrigin('landing');
        $this->waitPaymentOutcomeProcess->startInBackground($paymentId, $returnHmac, $merchantReference);
    }
    public function getPaymentOutcome(?PaymentId $paymentId, ?string $returnHmac = null, ?string $merchantReference = null): PaymentOutcomeResponse
    {
        StoreContext::getInstance()->setOrigin('landing');
        return new PaymentOutcomeResponse($this->waitPaymentOutcomeProcess->getPaymentOutcome($paymentId, $returnHmac, $merchantReference));
    }
    public function updateOrderStatus(PaymentId $paymentId, ?string $returnHmac = null): UpdateStatusResponse
    {
        $this->waitPaymentOutcomeProcess->updateOrderStatus($paymentId, $returnHmac);
        return new UpdateStatusResponse();
    }
    public function getPaymentTransaction(string $merchantReference): PaymentTransactionResponse
    {
        return new PaymentTransactionResponse($this->paymentTransactionRepository->getByMerchantReference($merchantReference));
    }
}
