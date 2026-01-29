<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
/**
 * Class PaymentOperation.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Payment
 */
class PaymentOperation
{
    private PaymentId $id;
    private Amount $amount;
    private StatusCode $statusCode;
    private string $status;
    /**
     * @param PaymentId $id
     * @param Amount $amount
     * @param StatusCode $statusCode
     * @param string $status
     */
    public function __construct(PaymentId $id, Amount $amount, StatusCode $statusCode, string $status)
    {
        $this->id = $id;
        $this->amount = $amount;
        $this->statusCode = $statusCode;
        $this->status = $status;
    }
    public function getId(): PaymentId
    {
        return $this->id;
    }
    public function getAmount(): Amount
    {
        return $this->amount;
    }
    public function getStatusCode(): StatusCode
    {
        return $this->statusCode;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
}
