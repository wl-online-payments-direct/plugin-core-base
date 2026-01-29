<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\PaymentTransaction;

use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
/**
 * Class PaymentTransactionLockEntity.
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\PaymentTransaction
 */
class PaymentTransactionLockEntity extends Entity
{
    public const CLASS_NAME = __CLASS__;
    protected string $storeId;
    protected string $transactionId;
    protected string $merchantReference;
    protected int $lockTimestamp = 0;
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('storeId');
        $indexMap->addStringIndex('transactionId');
        $indexMap->addStringIndex('merchantReference');
        $indexMap->addIntegerIndex('lockTimestamp');
        return new EntityConfiguration($indexMap, 'PaymentTransactionLock');
    }
    public function inflate(array $data): void
    {
        parent::inflate($data);
        $this->storeId = $data['storeId'];
        $this->transactionId = $data['transactionId'];
        $this->merchantReference = $data['merchantReference'];
        $this->lockTimestamp = $data['lockTimestamp'];
    }
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['storeId'] = $this->storeId;
        $data['transactionId'] = $this->transactionId;
        $data['merchantReference'] = $this->merchantReference;
        $data['lockTimestamp'] = $this->lockTimestamp;
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
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }
    public function setTransactionId(string $transactionId): void
    {
        $this->transactionId = $transactionId;
    }
    public function getMerchantReference(): string
    {
        return $this->merchantReference;
    }
    public function setMerchantReference(string $merchantReference): void
    {
        $this->merchantReference = $merchantReference;
    }
    public function getLockTimestamp(): int
    {
        return $this->lockTimestamp;
    }
    public function setLockTimestamp(int $lockTimestamp): void
    {
        $this->lockTimestamp = $lockTimestamp;
    }
}
