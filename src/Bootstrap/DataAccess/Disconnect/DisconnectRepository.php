<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\Disconnect;

use DateTime;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Disconnect\Repositories\DisconnectRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Interfaces\RepositoryInterface;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\Operators;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\QueryFilter;
/**
 * Class DisconnectRepository
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\Disconnect
 */
class DisconnectRepository implements DisconnectRepositoryInterface
{
    protected StoreContext $storeContext;
    protected RepositoryInterface $repository;
    /**
     * @param StoreContext $storeContext
     * @param RepositoryInterface $repository
     */
    public function __construct(StoreContext $storeContext, RepositoryInterface $repository)
    {
        $this->storeContext = $storeContext;
        $this->repository = $repository;
    }
    /**
     * @inheritDoc
     */
    public function getDisconnectTime(): ?DateTime
    {
        $entity = $this->getDisconnectTimeEntity();
        return $entity ? $entity->getDate() : null;
    }
    /**
     * @inheritDoc
     */
    public function setDisconnectTime(DateTime $disconnectTime): void
    {
        $existingDisconnectTime = $this->getDisconnectTimeEntity();
        if ($existingDisconnectTime) {
            $existingDisconnectTime->setDate($disconnectTime);
            $existingDisconnectTime->setStoreId($this->storeContext->getStoreId());
            $this->repository->update($existingDisconnectTime);
            return;
        }
        $entity = new DisconnectTime();
        $entity->setStoreId($this->storeContext->getStoreId());
        $entity->setDate($disconnectTime);
        $this->repository->save($entity);
    }
    /**
     * @inheritDoc
     */
    public function deleteDisconnectTime(): void
    {
        $disconnectTime = $this->getDisconnectTimeEntity();
        if (!$disconnectTime) {
            return;
        }
        $this->repository->delete($disconnectTime);
    }
    /**
     * @return DisconnectTime|null
     *
     * @throws QueryFilterInvalidParamException
     */
    protected function getDisconnectTimeEntity(): ?DisconnectTime
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId());
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->repository->selectOne($queryFilter);
    }
}
