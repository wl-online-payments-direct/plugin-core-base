<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories\WebhookSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\WebhookSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Interfaces\RepositoryInterface;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\Operators;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\QueryFilter;
/**
 * Class WebhookSettingsRepository
 *
 * Webhook settings are global (store-wide), so they are keyed by storeId only - not per connection mode.
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings
 */
class WebhookSettingsRepository implements WebhookSettingsRepositoryInterface
{
    private RepositoryInterface $repository;
    private StoreContext $storeContext;
    /**
     * @param RepositoryInterface $repository
     * @param StoreContext $storeContext
     */
    public function __construct(RepositoryInterface $repository, StoreContext $storeContext)
    {
        $this->repository = $repository;
        $this->storeContext = $storeContext;
    }
    /**
     * @inheritDoc
     */
    public function getWebhookSettings(): ?WebhookSettings
    {
        /** @var WebhookSettingsEntity | null $entity */
        $entity = $this->repository->selectOne($this->getBaseQuery());
        return $entity ? $entity->getWebhookSettings() : null;
    }
    /**
     * @inheritDoc
     */
    public function saveWebhookSettings(WebhookSettings $webhookSettings): void
    {
        /** @var WebhookSettingsEntity | null $existingEntity */
        $existingEntity = $this->repository->selectOne($this->getBaseQuery());
        if ($existingEntity) {
            $existingEntity->setWebhookSettings($webhookSettings);
            $this->repository->update($existingEntity);
            return;
        }
        $entity = new WebhookSettingsEntity();
        $entity->setStoreId($this->storeContext->getStoreId());
        $entity->setWebhookSettings($webhookSettings);
        $this->repository->save($entity);
    }
    private function getBaseQuery(): QueryFilter
    {
        $queryFilter = new QueryFilter();
        return $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId());
    }
}
