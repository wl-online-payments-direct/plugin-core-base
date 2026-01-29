<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\PaymentTransaction;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentTransaction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories\PaymentTransactionRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Time\TimeProviderInterface;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Interfaces\RepositoryInterface;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\Operators;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\QueryFilter;
/**
 * Class PaymentTransactionRepository.
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\PaymentTransaction
 */
class PaymentTransactionRepository implements PaymentTransactionRepositoryInterface
{
    private RepositoryInterface $repository;
    private RepositoryInterface $lockRepository;
    private StoreContext $storeContext;
    private TimeProviderInterface $timeProvider;
    public function __construct(RepositoryInterface $repository, RepositoryInterface $lockRepository, StoreContext $storeContext, TimeProviderInterface $timeProvider)
    {
        $this->repository = $repository;
        $this->lockRepository = $lockRepository;
        $this->storeContext = $storeContext;
        $this->timeProvider = $timeProvider;
    }
    public function save(PaymentTransaction $paymentTransaction): void
    {
        $existingTransaction = $this->getPaymentTransactionEntity($paymentTransaction->getPaymentId(), null, $paymentTransaction->getPaymentLinkId());
        $paymentTransaction->setUpdatedAt($this->timeProvider->getCurrentLocalTime());
        if ($existingTransaction) {
            $existingTransaction->setPaymentTransaction($paymentTransaction);
            $this->repository->update($existingTransaction);
            return;
        }
        $paymentTransaction->setCreatedAt($this->timeProvider->getCurrentLocalTime());
        $entity = new PaymentTransactionEntity();
        $entity->setStoreId($this->storeContext->getStoreId());
        $entity->setPaymentTransaction($paymentTransaction);
        $this->repository->save($entity);
    }
    public function updatePaymentId(PaymentTransaction $paymentTransaction, PaymentId $paymentId): void
    {
        $existingTransaction = $this->getPaymentTransactionEntity(null, null, $paymentTransaction->getPaymentLinkId());
        if (!$existingTransaction) {
            return;
        }
        $paymentTransaction->setUpdatedAt($this->timeProvider->getCurrentLocalTime());
        $paymentTransaction->setPaymentId($paymentId);
        $existingTransaction->setPaymentTransaction($paymentTransaction);
        $this->repository->update($existingTransaction);
    }
    public function get(PaymentId $paymentId, ?string $returnHmac = null): ?PaymentTransaction
    {
        $entity = $this->getPaymentTransactionEntity($paymentId, $returnHmac);
        return $entity ? $entity->getPaymentTransaction() : null;
    }
    public function getByPaymentLinkId(string $paymentLinkId): ?PaymentTransaction
    {
        $entity = $this->getPaymentTransactionEntity(null, null, $paymentLinkId);
        return $entity ? $entity->getPaymentTransaction() : null;
    }
    /**
     * @param string $reference
     *
     * @return PaymentTransaction|null
     * @throws QueryFilterInvalidParamException
     */
    public function getByMerchantReference(string $reference): ?PaymentTransaction
    {
        $queryFilter = new QueryFilter();
        // orderBy is set to DESC to fetch the last transaction from the database
        $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId())->where('merchantReference', Operators::EQUALS, $reference)->orderBy('createdAtTimestamp', QueryFilter::ORDER_DESC);
        /** @var PaymentTransactionEntity| null $entity */
        $entity = $this->repository->selectOne($queryFilter);
        return $entity ? $entity->getPaymentTransaction() : null;
    }
    private function getPaymentTransactionEntity(?PaymentId $paymentId, ?string $returnHmac = null, ?string $paymentLinkId = null): ?PaymentTransactionEntity
    {
        if (!$paymentId && !$paymentLinkId) {
            return null;
        }
        $queryFilter = new QueryFilter();
        $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId());
        if (null !== $paymentId) {
            $queryFilter->where('transactionId', Operators::EQUALS, $paymentId->getTransactionId());
        }
        if (null !== $returnHmac) {
            $queryFilter->where('returnHmac', Operators::EQUALS, $returnHmac);
        }
        if (null !== $paymentLinkId) {
            $queryFilter->where('paymentLinkId', Operators::EQUALS, $paymentLinkId);
        }
        /** @var PaymentTransactionEntity|null $entity */
        $entity = $this->repository->selectOne($queryFilter);
        return $entity;
    }
    public function lockOrderCreation(?PaymentId $paymentId): bool
    {
        $entity = $this->getPaymentTransactionEntity($paymentId);
        if (null === $entity) {
            return \false;
        }
        $lockTimestampCutoff = $this->timeProvider->getCurrentLocalTime()->getTimestamp() - 30;
        $paymentTransactionLock = $this->getPaymentTransactionLockEntity($entity->getPaymentTransaction());
        if (null !== $paymentTransactionLock && $paymentTransactionLock->getLockTimestamp() >= $lockTimestampCutoff) {
            return \false;
        }
        // Remove expired lock record
        if (null !== $paymentTransactionLock) {
            $this->lockRepository->delete($paymentTransactionLock);
        }
        try {
            $lock = new PaymentTransactionLockEntity();
            $lock->setStoreId($this->storeContext->getStoreId());
            $lock->setTransactionId($entity->getPaymentTransaction()->getPaymentId()->getTransactionId());
            $lock->setMerchantReference($entity->getPaymentTransaction()->getMerchantReference());
            $lock->setLockTimestamp($this->timeProvider->getCurrentLocalTime()->getTimestamp());
            $this->lockRepository->save($lock);
        } catch (\Throwable $e) {
            return \false;
        }
        return \true;
    }
    public function unlockOrderCreation(?PaymentId $paymentId): bool
    {
        $entity = $this->getPaymentTransactionEntity($paymentId);
        if (null === $entity) {
            return \false;
        }
        $paymentTransactionLock = $this->getPaymentTransactionLockEntity($entity->getPaymentTransaction());
        if (null !== $paymentTransactionLock) {
            $this->lockRepository->delete($paymentTransactionLock);
        }
        return \true;
    }
    private function getPaymentTransactionLockEntity(PaymentTransaction $paymentTransaction): ?PaymentTransactionLockEntity
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId())->where('transactionId', Operators::EQUALS, $paymentTransaction->getPaymentId()->getTransactionId())->where('merchantReference', Operators::EQUALS, $paymentTransaction->getMerchantReference());
        /** @var PaymentTransactionLockEntity|null $entity */
        $entity = $this->lockRepository->selectOne($queryFilter);
        return $entity;
    }
}
