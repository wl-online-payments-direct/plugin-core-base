<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentTransaction;
/**
 * Interface PaymentTransactionRepositoryInterface.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories
 */
interface PaymentTransactionRepositoryInterface
{
    public function save(PaymentTransaction $paymentTransaction): void;
    public function updatePaymentId(PaymentTransaction $paymentTransaction, PaymentId $paymentId): void;
    public function get(PaymentId $paymentId, ?string $returnHmac = null): ?PaymentTransaction;
    public function getByPaymentLinkId(string $paymentLinkId): ?PaymentTransaction;
    public function getByMerchantReference(string $reference): ?PaymentTransaction;
    public function lockOrderCreation(?PaymentId $paymentId): bool;
    public function unlockOrderCreation(?PaymentId $paymentId): bool;
}
