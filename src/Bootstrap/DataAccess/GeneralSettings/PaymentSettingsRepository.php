<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories\PaymentSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ActiveConnectionProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories\PaymentSettingsRepositoryInterface as DomainPaymentSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Interfaces\RepositoryInterface;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\Operators;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\QueryFilter;
/**
 * Class PaymentSettingsRepository
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings
 */
class PaymentSettingsRepository implements PaymentSettingsRepositoryInterface, DomainPaymentSettingsRepositoryInterface
{
    private RepositoryInterface $repository;
    private StoreContext $storeContext;
    private ActiveConnectionProvider $activeConnectionProvider;
    /**
     * @param RepositoryInterface $repository
     * @param StoreContext $storeContext
     * @param ActiveConnectionProvider $activeConnectionProvider
     */
    public function __construct(RepositoryInterface $repository, StoreContext $storeContext, ActiveConnectionProvider $activeConnectionProvider)
    {
        $this->repository = $repository;
        $this->storeContext = $storeContext;
        $this->activeConnectionProvider = $activeConnectionProvider;
    }
    /**
     * @inheritDoc
     */
    public function getPaymentSettings(): ?PaymentSettings
    {
        $activeConnection = $this->activeConnectionProvider->get();
        if (null === $activeConnection) {
            return null;
        }
        /** @var PaymentSettingsConfigEntity | null $entity */
        $entity = $this->repository->selectOne($this->getBaseQuery());
        return $entity ? $entity->getPaymentSettings() : null;
    }
    /**
     * @inheritDoc
     */
    public function savePaymentSettings(PaymentSettings $paymentSettings): void
    {
        $activeConnection = $this->activeConnectionProvider->get();
        if (null === $activeConnection) {
            return;
        }
        /** @var PaymentSettingsConfigEntity | null $existingEntity */
        $existingEntity = $this->repository->selectOne($this->getBaseQuery());
        if ($existingEntity) {
            $existingEntity->setPaymentSettings($paymentSettings);
            $this->repository->update($existingEntity);
            return;
        }
        $entity = new PaymentSettingsConfigEntity();
        $entity->setStoreId($this->storeContext->getStoreId());
        $entity->setMode($activeConnection->getMode());
        $entity->setPaymentSettings($paymentSettings);
        $this->repository->save($entity);
    }
    private function getBaseQuery(): QueryFilter
    {
        $activeConnection = $this->activeConnectionProvider->get();
        $mode = $activeConnection ? $activeConnection->getMode() : null;
        $queryFilter = new QueryFilter();
        return $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId())->where('mode', Operators::EQUALS, (string) $mode);
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
}
