<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\CheckoutAPI\CheckoutAPI;
use WOP\OnlinePayments\Core\Bootstrap\DataAccess\PaymentTransaction\PendingTransactionsRepository;
use WOP\OnlinePayments\Core\Bootstrap\TaskExecution\TenantAwareTask;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\Infrastructure\Serializer\Interfaces\Serializable;
use WOP\OnlinePayments\Core\Infrastructure\Serializer\Serializer;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
/**
 * Class TransactionStatusCheckTask.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses
 */
class TransactionStatusCheckTask extends TenantAwareTask
{
    public function __construct(string $tenantId = '')
    {
        parent::__construct($tenantId);
    }
    /**
     * @inheritDoc
     */
    protected function doExecute(): void
    {
        foreach ($this->getPendingTransactionsRepository()->get() as $paymentTransaction) {
            StoreContext::getInstance()->setOrigin('fallback');
            CheckoutAPI::get()->forTenant($this->tenantId)->payment($paymentTransaction->getStoreId())->updateOrderStatus($paymentTransaction->getPaymentTransaction()->getPaymentId(), $paymentTransaction->getPaymentTransaction()->getReturnHmac());
            $this->reportAlive();
        }
        $this->reportProgress(100);
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return ['tenantId' => $this->tenantId];
    }
    /**
     * @inheritDoc
     */
    public static function fromArray(array $array): Serializable
    {
        return new static($array['tenantId'] ?? '');
    }
    /**
     * @inheritDoc
     */
    public function serialize(): string
    {
        return Serializer::serialize($this->toArray());
    }
    /**
     * @inheritDoc
     */
    public function unserialize(string $serialized): void
    {
        $data = Serializer::unserialize($serialized);
        $this->tenantId = $data['tenantId'] ?? '';
    }
    protected function getPendingTransactionsRepository(): PendingTransactionsRepository
    {
        return ServiceRegister::getService(PendingTransactionsRepository::class);
    }
}
