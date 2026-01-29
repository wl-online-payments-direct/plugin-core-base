<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\CheckoutAPI\CheckoutAPI;
use WOP\OnlinePayments\Core\Bootstrap\DataAccess\PaymentTransaction\PendingTransactionsRepository;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Task;
/**
 * Class TransactionStatusCheckTask.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses
 */
class TransactionStatusCheckTask extends Task
{
    public function execute(): void
    {
        foreach ($this->getPendingTransactionsRepository()->get() as $paymentTransaction) {
            StoreContext::getInstance()->setOrigin('fallback');
            CheckoutAPI::get()->payment($paymentTransaction->getStoreId())->updateOrderStatus($paymentTransaction->getPaymentTransaction()->getPaymentId(), $paymentTransaction->getPaymentTransaction()->getReturnHmac());
            $this->reportAlive();
        }
        $this->reportProgress(100);
    }
    protected function getPendingTransactionsRepository(): PendingTransactionsRepository
    {
        return ServiceRegister::getService(PendingTransactionsRepository::class);
    }
}
