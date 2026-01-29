<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Order;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusError;
/**
 * Class OrderDetails
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Order
 */
class OrderDetails
{
    private Amount $amount;
    /** @var OrderPayment[] */
    private array $payments;
    private OrderAction $capture;
    private OrderAction $refund;
    private OrderAction $cancel;
    /** @var StatusError[] */
    private array $errors;
    /**
     * @param Amount $amount
     * @param OrderPayment[] $payments
     * @param OrderAction $capture
     * @param OrderAction $refund
     * @param OrderAction $cancel
     * @param StatusError[] $errors
     */
    public function __construct(Amount $amount, array $payments, OrderAction $capture, OrderAction $refund, OrderAction $cancel, array $errors)
    {
        $this->amount = $amount;
        $this->payments = $payments;
        $this->capture = $capture;
        $this->refund = $refund;
        $this->cancel = $cancel;
        $this->errors = $errors;
    }
    public function getAmount(): Amount
    {
        return $this->amount;
    }
    public function getPayments(): array
    {
        return $this->payments;
    }
    public function getCapture(): OrderAction
    {
        return $this->capture;
    }
    public function getRefund(): OrderAction
    {
        return $this->refund;
    }
    public function getCancel(): OrderAction
    {
        return $this->cancel;
    }
    public function getErrors(): array
    {
        return $this->errors;
    }
}
