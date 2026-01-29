<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Order\Services\Order;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Currency;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Order\OrderAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Order\OrderDetails;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Order\OrderPayment;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories\PaymentTransactionRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusCode;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\PaymentsProxyInterface;
/**
 * Class OrderService
 *
 * @package OnlinePayments\Core\BusinessLogic\Order\Services\Order
 */
class OrderService
{
    private const STATUS_PAYMENT_CAPTURED = 'CAPTURED';
    private const STATUS_PAYMENT_PENDING_CAPTURE = 'PENDING_CAPTURE';
    private PaymentTransactionRepositoryInterface $paymentTransactionRepository;
    private PaymentsProxyInterface $paymentsProxy;
    public function __construct(PaymentTransactionRepositoryInterface $paymentTransactionRepository, PaymentsProxyInterface $paymentsProxy)
    {
        $this->paymentTransactionRepository = $paymentTransactionRepository;
        $this->paymentsProxy = $paymentsProxy;
    }
    public function getDetails(string $merchantReference): OrderDetails
    {
        $transaction = $this->paymentTransactionRepository->getByMerchantReference($merchantReference);
        if (!$transaction || $transaction->getStatusCode()->equals(StatusCode::incomplete()) || !$transaction->getPaymentId()) {
            throw new \Exception('Cannot find Worldline transaction');
        }
        try {
            $paymentDetails = $this->paymentsProxy->getPaymentDetails($transaction->getPaymentId());
        } catch (\Exception $e) {
            throw new \Exception('Could not retrieve transaction details. Reason: ' . $e->getMessage());
        }
        $payments = [];
        foreach ($paymentDetails->getOperations() as $operation) {
            if (!in_array($operation->getStatus(), [self::STATUS_PAYMENT_PENDING_CAPTURE, self::STATUS_PAYMENT_CAPTURED])) {
                continue;
            }
            $payment = $this->paymentsProxy->tryToGetPayment($operation->getId());
            if (!$payment || array_key_exists($payment->getProductId(), $payments)) {
                continue;
            }
            $payments[$payment->getProductId()] = new OrderPayment($operation->getId(), $paymentDetails->getStatus(), $operation->getAmount(), $paymentDetails->getPaymentSpecificOutput() ? $paymentDetails->getPaymentSpecificOutput()->getSurchargeAmount() : null, $payment->getPaymentMethodName(), $payment->getProductId(), $paymentDetails->getPaymentSpecificOutput() ? $paymentDetails->getPaymentSpecificOutput()->getFraudResult() : null, $paymentDetails->getPaymentSpecificOutput() ? $paymentDetails->getPaymentSpecificOutput()->getThreeDsLiability() : null, $paymentDetails->getPaymentSpecificOutput() ? $paymentDetails->getPaymentSpecificOutput()->getThreeDsExemptionType() : null);
        }
        if (empty($payments) && $payment = $this->paymentsProxy->tryToGetPayment($transaction->getPaymentId())) {
            $payments[$payment->getProductId()] = new OrderPayment($transaction->getPaymentId(), $paymentDetails->getStatus(), $paymentDetails->getAmount(), $paymentDetails->getPaymentSpecificOutput()->getSurchargeAmount(), $payment->getPaymentMethodName(), $payment->getProductId(), $paymentDetails->getPaymentSpecificOutput()->getFraudResult(), $paymentDetails->getPaymentSpecificOutput()->getThreeDsLiability(), $paymentDetails->getPaymentSpecificOutput()->getThreeDsExemptionType());
        }
        $notAvailableAmount = $paymentDetails->getAmounts()->getCapturedAmount()->plus($paymentDetails->getAmounts()->getCaptureRequestedAmount())->plus($paymentDetails->getAmounts()->getCancelledAmount())->getValue();
        $amountToCapture = $paymentDetails->getAmount()->getValue() - $notAvailableAmount;
        $capturableAmount = Amount::fromInt(!$paymentDetails->getStatusOutput()->isAuthorized() || $amountToCapture < 0 ? 0 : $amountToCapture, $paymentDetails->getAmount()->getCurrency());
        $amountToRefund = $paymentDetails->getAmounts()->getCapturedAmount()->minus($paymentDetails->getAmounts()->getRefundedAmount())->minus($paymentDetails->getAmounts()->getRefundRequestedAmount())->getValue();
        $refundableAmount = Amount::fromInt(!$paymentDetails->getStatusOutput()->isRefundable() || $amountToRefund < 0 ? 0 : $amountToRefund, $paymentDetails->getAmount()->getCurrency());
        $amountToCancel = $paymentDetails->getAmount()->getValue() - $notAvailableAmount;
        $cancellableAmount = Amount::fromInt(!$paymentDetails->getStatusOutput()->isCancellable() || $amountToCancel < 0 ? 0 : $amountToCancel, $paymentDetails->getAmount()->getCurrency());
        return new OrderDetails($this->getOrderAmount($payments), $payments, new OrderAction($paymentDetails->getStatusOutput()->isAuthorized(), $paymentDetails->getAmounts()->getCapturedAmount(), $paymentDetails->getAmounts()->getCaptureRequestedAmount(), $capturableAmount), new OrderAction($paymentDetails->getStatusOutput()->isRefundable(), $paymentDetails->getAmounts()->getRefundedAmount(), $paymentDetails->getAmounts()->getRefundRequestedAmount(), $refundableAmount), new OrderAction($paymentDetails->getStatusOutput()->isCancellable(), $paymentDetails->getAmounts()->getCancelledAmount(), Amount::fromInt(0, $paymentDetails->getAmount()->getCurrency()), $cancellableAmount), $paymentDetails->getStatusOutput()->getErrors());
    }
    /**
     * @param OrderPayment[] $payments
     * @return Amount
     */
    private function getOrderAmount(array $payments): Amount
    {
        if (empty($payments)) {
            return Amount::fromInt(0, Currency::getDefault());
        }
        $currency = reset($payments)->getAmount()->getCurrency();
        $amount = Amount::fromInt(0, $currency);
        foreach ($payments as $payment) {
            $amount = $amount->plus($payment->getAmount());
        }
        return $amount;
    }
}
