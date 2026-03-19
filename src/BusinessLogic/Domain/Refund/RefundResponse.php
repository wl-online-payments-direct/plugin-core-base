<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Refund;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusCode;
/**
 * Class RefundResponse.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Refund
 */
class RefundResponse
{
    private PaymentId $id;
    private StatusCode $statusCode;
    private string $status;
    private Amount $amount;
    /**
     * @param PaymentId $id
     * @param StatusCode $statusCode
     * @param string $status
     * @param Amount $amount
     */
    public function __construct(PaymentId $id, StatusCode $statusCode, string $status, Amount $amount)
    {
        $this->id = $id;
        $this->statusCode = $statusCode;
        $this->status = $status;
        $this->amount = $amount;
    }
    public function getId(): PaymentId
    {
        return $this->id;
    }
    public function getStatusCode(): StatusCode
    {
        return $this->statusCode;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getAmount(): Amount
    {
        return $this->amount;
    }
}
