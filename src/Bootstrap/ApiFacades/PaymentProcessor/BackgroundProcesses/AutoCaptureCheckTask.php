<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Order\OrderAPI\OrderAPI;
use WOP\OnlinePayments\Core\Bootstrap\DataAccess\PaymentTransaction\AuthorizedTransactionsRepository;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Capture\CaptureRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Task;
/**
 * Class AutoCaptureCheckTask.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses
 */
class AutoCaptureCheckTask extends Task
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        foreach ($this->getAuthorizedTransactionsRepository()->get() as $paymentTransaction) {
            StoreContext::getInstance()->setOrigin('order.autocapture');
            OrderAPI::get()->capture($paymentTransaction->getStoreId())->handle(new CaptureRequest($paymentTransaction->getPaymentTransaction()->getPaymentId()));
        }
        $this->reportProgress(100);
    }
    protected function getAuthorizedTransactionsRepository(): AuthorizedTransactionsRepository
    {
        return ServiceRegister::getService(AuthorizedTransactionsRepository::class);
    }
}
