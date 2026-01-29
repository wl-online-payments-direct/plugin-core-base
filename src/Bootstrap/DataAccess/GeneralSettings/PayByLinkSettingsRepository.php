<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories\PayByLinkSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ActiveConnectionProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PayByLinkSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Interfaces\RepositoryInterface;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\Operators;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\QueryFilter;
/**
 * Class PayByLinkSettingsRepository
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings
 */
class PayByLinkSettingsRepository implements PayByLinkSettingsRepositoryInterface
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
    public function getPayByLinkSettings(): ?PayByLinkSettings
    {
        $activeConnection = $this->activeConnectionProvider->get();
        if (null === $activeConnection) {
            return null;
        }
        /** @var PayByLinkSettingsEntity | null $entity */
        $entity = $this->repository->selectOne($this->getBaseQuery());
        return $entity ? $entity->getPayByLinkSettings() : null;
    }
    /**
     * @inheritDoc
     */
    public function savePayByLinkSettings(PayByLinkSettings $payByLinkSettings): void
    {
        $activeConnection = $this->activeConnectionProvider->get();
        if (null === $activeConnection) {
            return;
        }
        /** @var PayByLinkSettingsEntity | null $existingEntity */
        $existingEntity = $this->repository->selectOne($this->getBaseQuery());
        if ($existingEntity) {
            $existingEntity->setPayByLinkSettings($payByLinkSettings);
            $this->repository->update($existingEntity);
            return;
        }
        $entity = new PayByLinkSettingsEntity();
        $entity->setStoreId($this->storeContext->getStoreId());
        $entity->setMode($activeConnection->getMode());
        $entity->setPayByLinkSettings($payByLinkSettings);
        $this->repository->save($entity);
    }
    /**
     * @inheritDoc
     *
     * @throws QueryFilterInvalidParamException
     */
    public function deleteByMode(string $mode): void
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId())->where('mode', Operators::EQUALS, $mode);
        $this->repository->deleteWhere($queryFilter);
    }
    private function getBaseQuery(): QueryFilter
    {
        $activeConnection = $this->activeConnectionProvider->get();
        $mode = $activeConnection ? $activeConnection->getMode() : null;
        $queryFilter = new QueryFilter();
        return $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId())->where('mode', Operators::EQUALS, (string) $mode);
    }
}
