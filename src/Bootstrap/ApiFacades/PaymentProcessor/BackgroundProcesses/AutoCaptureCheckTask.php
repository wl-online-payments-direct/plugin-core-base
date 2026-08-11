<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Order\OrderAPI\OrderAPI;
use WOP\OnlinePayments\Core\Bootstrap\DataAccess\PaymentTransaction\AuthorizedTransactionsRepository;
use WOP\OnlinePayments\Core\Bootstrap\TaskExecution\TenantAwareTask;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Capture\CaptureRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\Infrastructure\Serializer\Interfaces\Serializable;
use WOP\OnlinePayments\Core\Infrastructure\Serializer\Serializer;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
/**
 * Class AutoCaptureCheckTask.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\BackgroundProcesses
 */
class AutoCaptureCheckTask extends TenantAwareTask
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
        foreach ($this->getAuthorizedTransactionsRepository()->get() as $paymentTransaction) {
            StoreContext::getInstance()->setOrigin('order.autocapture');
            OrderAPI::get()->forTenant($this->tenantId)->capture($paymentTransaction->getStoreId())->handle(new CaptureRequest($paymentTransaction->getPaymentTransaction()->getPaymentId()));
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
    protected function getAuthorizedTransactionsRepository(): AuthorizedTransactionsRepository
    {
        return ServiceRegister::getService(AuthorizedTransactionsRepository::class);
    }
}
