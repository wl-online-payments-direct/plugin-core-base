<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout;

/**
 * Class SurchargeResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Checkout
 */
class SurchargeResponse
{
    private Amount $netAmount;
    private Amount $surchargeAmount;
    private Amount $totalAmount;
    /**
     * @param Amount $netAmount
     * @param Amount $surchargeAmount
     * @param Amount $totalAmount
     */
    public function __construct(Amount $netAmount, Amount $surchargeAmount, Amount $totalAmount)
    {
        $this->netAmount = $netAmount;
        $this->surchargeAmount = $surchargeAmount;
        $this->totalAmount = $totalAmount;
    }
    public function getNetAmount(): Amount
    {
        return $this->netAmount;
    }
    public function getSurchargeAmount(): Amount
    {
        return $this->surchargeAmount;
    }
    public function getTotalAmount(): Amount
    {
        return $this->totalAmount;
    }
}
