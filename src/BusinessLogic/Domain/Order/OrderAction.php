<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Order;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
class OrderAction
{
    private bool $possible;
    private Amount $done;
    private Amount $pending;
    private Amount $available;
    /**
     * @param bool $possible
     * @param Amount $done
     * @param Amount $pending
     * @param Amount $available
     */
    public function __construct(bool $possible, Amount $done, Amount $pending, Amount $available)
    {
        $this->possible = $possible;
        $this->done = $done;
        $this->pending = $pending;
        $this->available = $available;
    }
    public function isPossible(): bool
    {
        return $this->possible;
    }
    public function getDone(): Amount
    {
        return $this->done;
    }
    public function getPending(): Amount
    {
        return $this->pending;
    }
    public function getAvailable(): Amount
    {
        return $this->available;
    }
}
