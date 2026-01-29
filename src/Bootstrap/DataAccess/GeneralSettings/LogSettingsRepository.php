<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories\LogSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ActiveConnectionProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\LogSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Interfaces\RepositoryInterface;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\Operators;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\QueryFilter;
/**
 * Class LogSettingsRepository
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings
 */
class LogSettingsRepository implements LogSettingsRepositoryInterface
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
    public function getLogSettings(): ?LogSettings
    {
        $activeConnection = $this->activeConnectionProvider->get();
        if (null === $activeConnection) {
            return null;
        }
        /** @var LogSettingsEntity|null $entity */
        $entity = $this->repository->selectOne($this->getBaseQuery());
        return $entity ? $entity->getLogSettings() : null;
    }
    /**
     * @inheritDoc
     */
    public function saveLogSettings(LogSettings $logSettings): void
    {
        $activeConnection = $this->activeConnectionProvider->get();
        if (null === $activeConnection) {
            return;
        }
        /** @var LogSettingsEntity|null $existingEntity */
        $existingEntity = $this->repository->selectOne($this->getBaseQuery());
        if ($existingEntity) {
            $existingEntity->setLogSettings($logSettings);
            $this->repository->update($existingEntity);
            return;
        }
        $entity = new LogSettingsEntity();
        $entity->setStoreId($this->storeContext->getStoreId());
        $entity->setMode($activeConnection->getMode());
        $entity->setLogSettings($logSettings);
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
