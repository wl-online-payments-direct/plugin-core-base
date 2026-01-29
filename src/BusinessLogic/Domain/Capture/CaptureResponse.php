<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Capture;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusCode;
/**
 * Class CaptureResponse.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Capture
 */
class CaptureResponse
{
    private PaymentId $id;
    private StatusCode $statusCode;
    private string $status;
    /**
     * @param PaymentId $id
     * @param StatusCode $statusCode
     * @param string $status
     */
    public function __construct(PaymentId $id, StatusCode $statusCode, string $status)
    {
        $this->id = $id;
        $this->statusCode = $statusCode;
        $this->status = $status;
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
}
