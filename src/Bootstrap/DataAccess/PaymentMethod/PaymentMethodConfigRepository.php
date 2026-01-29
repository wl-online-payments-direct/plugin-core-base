<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\PaymentMethod;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ActiveConnectionProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethod;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodCollection;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Repositories\PaymentConfigRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Repositories\PaymentMethodConfigRepositoryInterface;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Interfaces\RepositoryInterface;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\Operators;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\QueryFilter;
/**
 * Class PaymentMethodConfigRepository.
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\PaymentMethod
 */
class PaymentMethodConfigRepository implements PaymentMethodConfigRepositoryInterface, PaymentConfigRepositoryInterface
{
    private RepositoryInterface $repository;
    private StoreContext $storeContext;
    private ActiveConnectionProvider $activeConnectionProvider;
    public function __construct(RepositoryInterface $repository, StoreContext $storeContext, ActiveConnectionProvider $activeConnectionProvider)
    {
        $this->repository = $repository;
        $this->storeContext = $storeContext;
        $this->activeConnectionProvider = $activeConnectionProvider;
    }
    /**
     * @return PaymentMethodCollection
     */
    public function getPaymentMethods(): PaymentMethodCollection
    {
        $activeConnection = $this->activeConnectionProvider->get();
        if (null === $activeConnection) {
            return new PaymentMethodCollection();
        }
        $methods = $this->findPaymentMethodConfigs($this->getBaseQuery());
        return new PaymentMethodCollection(array_map(function (PaymentMethodConfigEntity $entity) {
            return $entity->getPaymentMethod();
        }, $methods));
    }
    /**
     * @param string $productId
     *
     * @return PaymentMethod|null
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getPaymentMethod(string $productId): ?PaymentMethod
    {
        $activeConnection = $this->activeConnectionProvider->get();
        if (null === $activeConnection) {
            return null;
        }
        $method = $this->findPaymentMethodConfig($this->getBaseQuery()->where('paymentProductId', Operators::EQUALS, $productId));
        return $method ? $method->getPaymentMethod() : null;
    }
    /**
     * @param PaymentMethod $paymentMethod
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function savePaymentMethod(PaymentMethod $paymentMethod): void
    {
        $activeConnection = $this->activeConnectionProvider->get();
        if (null === $activeConnection) {
            return;
        }
        $existingConfig = $this->findPaymentMethodConfig($this->getBaseQuery()->where('paymentProductId', Operators::EQUALS, (string) $paymentMethod->getProductId()));
        if ($existingConfig) {
            $existingConfig->setEnabled($paymentMethod->isEnabled());
            $existingConfig->setPaymentMethod($paymentMethod);
            $this->repository->update($existingConfig);
            return;
        }
        $entity = new PaymentMethodConfigEntity();
        $entity->setStoreId($this->storeContext->getStoreId());
        $entity->setMode($activeConnection->getMode());
        $entity->setPaymentProductId((string) $paymentMethod->getProductId());
        $entity->setEnabled($paymentMethod->isEnabled());
        $entity->setPaymentMethod($paymentMethod);
        $this->repository->save($entity);
    }
    /**
     * @return PaymentMethodCollection
     */
    public function getEnabled(): PaymentMethodCollection
    {
        $activeConnection = $this->activeConnectionProvider->get();
        if (null === $activeConnection) {
            return new PaymentMethodCollection();
        }
        $enabledMethods = $this->findPaymentMethodConfigs($this->getBaseQuery()->where('enabled', Operators::EQUALS, \true));
        return new PaymentMethodCollection(array_map(function (PaymentMethodConfigEntity $entity) {
            return $entity->getPaymentMethod();
        }, $enabledMethods));
    }
    /**
     * @param string $mode
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function deleteByMode(string $mode): void
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId())->where('mode', Operators::EQUALS, $mode);
        $this->repository->deleteWhere($queryFilter);
    }
    /**
     * @param QueryFilter $queryFilter
     *
     * @return PaymentMethodConfigEntity[]
     */
    private function findPaymentMethodConfigs(QueryFilter $queryFilter): array
    {
        /** @var PaymentMethodConfigEntity[] $configs */
        $configs = $this->repository->select($queryFilter);
        return $configs;
    }
    /**
     * @param QueryFilter $queryFilter
     *
     * @return PaymentMethodConfigEntity|null
     */
    private function findPaymentMethodConfig(QueryFilter $queryFilter): ?PaymentMethodConfigEntity
    {
        /** @var ?PaymentMethodConfigEntity $config */
        $config = $this->repository->selectOne($queryFilter);
        return $config;
    }
    private function getBaseQuery(): QueryFilter
    {
        $activeConnection = $this->activeConnectionProvider->get();
        $mode = $activeConnection ? $activeConnection->getMode() : null;
        $queryFilter = new QueryFilter();
        return $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId())->where('mode', Operators::EQUALS, (string) $mode);
    }
}
