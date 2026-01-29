<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Order;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
/**
 * Class OrderPayment
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Order
 */
class OrderPayment
{
    private PaymentId $id;
    private string $status;
    private Amount $amount;
    private ?Amount $surcharge;
    private ?string $paymentMethodName;
    private ?string $paymentMethodId;
    private ?string $fraudResult;
    private ?string $liability;
    private ?string $exemptionType;
    /**
     * @param PaymentId $id
     * @param string $status
     * @param Amount $amount
     * @param Amount|null $surcharge
     * @param string|null $paymentMethodName
     * @param string|null $paymentMethodId
     * @param string|null $fraudResult
     * @param string|null $liability
     * @param string|null $exemptionType
     */
    public function __construct(PaymentId $id, string $status, Amount $amount, ?Amount $surcharge, ?string $paymentMethodName, ?string $paymentMethodId, ?string $fraudResult, ?string $liability, ?string $exemptionType)
    {
        $this->id = $id;
        $this->status = $status;
        $this->amount = $amount;
        $this->surcharge = $surcharge;
        $this->paymentMethodName = $paymentMethodName;
        $this->paymentMethodId = $paymentMethodId;
        $this->fraudResult = $fraudResult;
        $this->liability = $liability;
        $this->exemptionType = $exemptionType;
    }
    public function getId(): PaymentId
    {
        return $this->id;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getAmount(): Amount
    {
        return $this->amount;
    }
    public function getSurcharge(): ?Amount
    {
        return $this->surcharge;
    }
    public function getPaymentMethodName(): ?string
    {
        return $this->paymentMethodName;
    }
    public function getPaymentMethodId(): ?string
    {
        return $this->paymentMethodId;
    }
    public function getFraudResult(): ?string
    {
        return $this->fraudResult;
    }
    public function getLiability(): ?string
    {
        return $this->liability;
    }
    public function getExemptionType(): ?string
    {
        return $this->exemptionType;
    }
}
