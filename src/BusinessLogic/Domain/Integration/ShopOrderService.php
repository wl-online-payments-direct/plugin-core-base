<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentDetails;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentTransaction;
/**
 * Interface ShopOrderService.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Integration
 */
interface ShopOrderService
{
    /**
     * Creates shop order from belonging cart if it doesnt already exists for a given payment transaction.
     *
     * @param PaymentTransaction $paymentTransaction
     * @param PaymentDetails $paymentDetails
     * @param string $newState
     *
     * @return void
     */
    public function createShopOrder(PaymentTransaction $paymentTransaction, PaymentDetails $paymentDetails, string $newState): void;
    /**
     * Updates shop order status.
     *
     * @param PaymentTransaction $paymentTransaction
     * @param PaymentDetails $paymentDetails
     * @param string $newState
     * @return void
     */
    public function updateStatus(PaymentTransaction $paymentTransaction, PaymentDetails $paymentDetails, string $newState): void;
    /**
     * Cancels the shop order (if order is even created) for a given payment transaction
     *
     * @param PaymentTransaction $paymentTransaction
     * @param PaymentDetails $paymentDetails
     * @param string $newState
     *
     * @return void
     */
    public function cancelShopOrder(PaymentTransaction $paymentTransaction, PaymentDetails $paymentDetails, string $newState): void;
    /**
     * Refunds the shop order for a given payment transaction
     *
     * @param PaymentTransaction $paymentTransaction
     * @param PaymentDetails $paymentDetails
     * @param string $newState
     *
     * @return void
     */
    public function refundShopOrder(PaymentTransaction $paymentTransaction, PaymentDetails $paymentDetails, string $newState): void;
}
