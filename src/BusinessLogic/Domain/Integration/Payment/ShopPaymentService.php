<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Integration\Payment;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethod;
/**
 * Interface ShopPaymentService
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Integration\Payment
 */
interface ShopPaymentService
{
    /**
     * @param PaymentMethod $paymentMethod
     *
     * @return void
     */
    public function savePaymentMethod(PaymentMethod $paymentMethod): void;
    /**
     * @param string $paymentProductId
     * @param bool $enabled
     *
     * @return void
     */
    public function enable(string $paymentProductId, bool $enabled): void;
    /**
     * @param string $mode
     *
     * @return void
     */
    public function deletePaymentMethods(string $mode): void;
}
