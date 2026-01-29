<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Order\ApiFacades\OrdersAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Order\OrderDetails;
/**
 * Class OrderDetailsResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\Order\ApiFacades\OrdersAPI\Response
 */
class OrderDetailsResponse extends Response
{
    private OrderDetails $orderDetails;
    public function __construct(OrderDetails $orderDetails)
    {
        $this->orderDetails = $orderDetails;
    }
    public function getOrderDetails(): OrderDetails
    {
        return $this->orderDetails;
    }
    public function toArray(): array
    {
        $details = $this->getOrderDetails();
        return ['amounts' => ['value' => $details->getAmount()->getValue(), 'currency' => $details->getAmount()->getCurrency()->getIsoCode()], 'payments' => array_map(function ($payment) {
            return ['id' => (string) $payment->getId(), 'status' => $payment->getStatus(), 'amount' => $payment->getAmount()->getValue(), 'currency' => $payment->getAmount()->getCurrency(), 'surchargeAmount' => $payment->getSurcharge() ? $payment->getSurcharge()->getValue() : 0];
        }, $details->getPayments()), 'errors' => array_map(function ($error) {
            return ['id' => $error->getId(), 'code' => $error->getErrorCode()];
        }, $details->getErrors()), 'capture' => ['possible' => $details->getCapture()->isPossible(), 'amount' => $details->getCapture()->getDone()->getValue(), 'pending' => $details->getCapture()->getPending()->getValue(), 'available' => $details->getCapture()->getAvailable()->getValue()], 'cancel' => ['possible' => $details->getCancel()->isPossible(), 'amount' => $details->getCancel()->getDone()->getValue(), 'pending' => $details->getCancel()->getPending()->getValue(), 'available' => $details->getCancel()->getAvailable()->getValue()], 'refund' => ['possible' => $details->getRefund()->isPossible(), 'amount' => $details->getRefund()->getDone()->getValue(), 'pending' => $details->getRefund()->getPending()->getValue(), 'available' => $details->getRefund()->getAvailable()->getValue()]];
    }
}
