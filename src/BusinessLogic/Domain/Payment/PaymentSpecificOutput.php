<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
/**
 * Class PaymentSpecificOutput.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Payment
 */
class PaymentSpecificOutput
{
    private ?string $productId;
    private ?string $fraudResult;
    private ?string $threeDsLiability;
    private ?string $threeDsExemptionType;
    private ?Amount $surchargeAmount;
    /**
     * @param string|null $productId
     * @param string|null $fraudResult
     * @param string|null $threeDsLiability
     * @param string|null $threeDsExemptionType
     * @param Amount|null $surchargeAmount
     */
    public function __construct(?string $productId, ?string $fraudResult, ?string $threeDsLiability, ?string $threeDsExemptionType, ?Amount $surchargeAmount)
    {
        $this->productId = $productId;
        $this->fraudResult = $fraudResult;
        $this->threeDsLiability = $threeDsLiability;
        $this->threeDsExemptionType = $threeDsExemptionType;
        $this->surchargeAmount = $surchargeAmount;
    }
    public function getProductId(): ?string
    {
        return $this->productId;
    }
    public function getFraudResult(): ?string
    {
        return $this->fraudResult;
    }
    public function getThreeDsLiability(): ?string
    {
        return $this->threeDsLiability;
    }
    public function getThreeDsExemptionType(): ?string
    {
        return $this->threeDsExemptionType;
    }
    public function getSurchargeAmount(): ?Amount
    {
        return $this->surchargeAmount;
    }
}
