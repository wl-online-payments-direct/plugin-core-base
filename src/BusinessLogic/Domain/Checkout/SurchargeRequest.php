<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout;

/**
 * Class Surcharge
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Checkout
 */
class SurchargeRequest
{
    private Amount $amount;
    private string $token;
    /**
     * @param Amount $amount
     * @param string $token
     */
    public function __construct(Amount $amount, string $token)
    {
        $this->amount = $amount;
        $this->token = $token;
    }
    /**
     * @return Amount
     */
    public function getAmount(): Amount
    {
        return $this->amount;
    }
    /**
     * @param Amount $amount
     * @return void
     */
    public function setAmount(Amount $amount): void
    {
        $this->amount = $amount;
    }
    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
    /**
     * @param string $token
     * @return void
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }
}
