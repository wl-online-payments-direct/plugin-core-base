<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\PaymentTransaction;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentTransaction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Time\TimeProviderInterface;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
/**
 * Class PaymentTransactionEntity.
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\PaymentTransaction
 */
class PaymentTransactionEntity extends Entity
{
    public const CLASS_NAME = __CLASS__;
    protected string $storeId;
    protected PaymentTransaction $paymentTransaction;
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('storeId');
        $indexMap->addStringIndex('transactionId');
        $indexMap->addStringIndex('merchantReference');
        $indexMap->addStringIndex('returnHmac');
        $indexMap->addIntegerIndex('statusCode');
        $indexMap->addIntegerIndex('createdAtTimestamp');
        $indexMap->addIntegerIndex('captureAtTimestamp');
        $indexMap->addIntegerIndex('updatedAtTimestamp');
        $indexMap->addStringIndex('paymentLinkId');
        return new EntityConfiguration($indexMap, 'PaymentTransaction');
    }
    public function inflate(array $data): void
    {
        parent::inflate($data);
        $this->storeId = $data['storeId'];
        /** @var TimeProviderInterface $timeProvider */
        $timeProvider = ServiceRegister::getService(TimeProviderInterface::class);
        $paymentTransaction = $data['paymentTransaction'] ?? [];
        $this->paymentTransaction = new PaymentTransaction($paymentTransaction['merchantReference'], $paymentTransaction['paymentId'] ? PaymentId::parse($paymentTransaction['paymentId']) : null, $paymentTransaction['returnHmac'], StatusCode::parse((int) $paymentTransaction['statusCode']), $paymentTransaction['customerId'], $paymentTransaction['cratedAt'] ? $timeProvider->getDateTime($paymentTransaction['cratedAt']) : null, $paymentTransaction['updatedAt'] ? $timeProvider->getDateTime($paymentTransaction['updatedAt']) : null, $paymentTransaction['returnedAt'] ? $timeProvider->getDateTime($paymentTransaction['returnedAt']) : null, $paymentTransaction['paymentMethod'] ?: null, $paymentTransaction['captureAt'] ? $timeProvider->getDateTime($paymentTransaction['captureAt']) : null, $paymentTransaction['paymentLinkId']);
    }
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['storeId'] = $this->storeId;
        $createdAt = $this->paymentTransaction->getCreatedAt();
        $updatedAt = $this->paymentTransaction->getUpdatedAt();
        $returnedAt = $this->paymentTransaction->getReturnedAt();
        $captureAt = $this->paymentTransaction->getCaptureAt();
        $data['paymentTransaction'] = ['merchantReference' => $this->paymentTransaction->getMerchantReference(), 'paymentId' => (string) $this->paymentTransaction->getPaymentId(), 'returnHmac' => $this->paymentTransaction->getReturnHmac(), 'statusCode' => $this->paymentTransaction->getStatusCode()->getCode(), 'customerId' => $this->paymentTransaction->getCustomerId(), 'cratedAt' => $createdAt ? $createdAt->getTimestamp() : null, 'updatedAt' => $updatedAt ? $updatedAt->getTimestamp() : null, 'returnedAt' => $returnedAt ? $returnedAt->getTimestamp() : null, 'paymentMethod' => $this->paymentTransaction->getPaymentMethod(), 'captureAt' => $captureAt ? $captureAt->getTimestamp() : null, 'paymentLinkId' => $this->paymentTransaction->getPaymentLinkId()];
        return $data;
    }
    public function getStoreId(): string
    {
        return $this->storeId;
    }
    public function setStoreId(string $storeId): void
    {
        $this->storeId = $storeId;
    }
    public function getPaymentTransaction(): PaymentTransaction
    {
        return $this->paymentTransaction;
    }
    public function setPaymentTransaction(PaymentTransaction $paymentTransaction): void
    {
        $this->paymentTransaction = $paymentTransaction;
    }
    public function getMerchantReference(): string
    {
        return $this->paymentTransaction->getMerchantReference();
    }
    public function getTransactionId(): string
    {
        return $this->paymentTransaction->getPaymentId() ? $this->paymentTransaction->getPaymentId()->getTransactionId() : '';
    }
    public function getReturnHmac(): string
    {
        return (string) $this->paymentTransaction->getReturnHmac();
    }
    public function getStatusCode(): int
    {
        return $this->paymentTransaction->getStatusCode()->getCode();
    }
    public function getCreatedAtTimestamp(): int
    {
        $createdAt = $this->paymentTransaction->getCreatedAt();
        return $createdAt ? $createdAt->getTimestamp() : 0;
    }
    public function getUpdatedAtTimestamp(): int
    {
        $updatedAt = $this->paymentTransaction->getUpdatedAt();
        return $updatedAt ? $updatedAt->getTimestamp() : 0;
    }
    public function getCaptureAtTimestamp(): int
    {
        $captureAt = $this->paymentTransaction->getCaptureAt();
        return $captureAt ? $captureAt->getTimestamp() : 0;
    }
    public function getPaymentLinkId(): string
    {
        return (string) $this->paymentTransaction->getPaymentLinkId();
    }
}
