<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\WebhookSettings;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\EntityConfiguration;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration\IndexMap;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Entity;
/**
 * Class WebhookSettingsEntity
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\GeneralSettings
 */
class WebhookSettingsEntity extends Entity
{
    public const CLASS_NAME = __CLASS__;
    protected string $storeId;
    protected WebhookSettings $webhookSettings;
    /**
     * @inheritDoc
     */
    public function getConfig(): EntityConfiguration
    {
        $indexMap = new IndexMap();
        $indexMap->addStringIndex('storeId');
        return new EntityConfiguration($indexMap, 'WebhookSettings');
    }
    public function inflate(array $data): void
    {
        parent::inflate($data);
        $this->storeId = $data['storeId'];
        $this->webhookSettings = new WebhookSettings($data['webhookSettings']['additionalWebhookUrls'] ?? []);
    }
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['storeId'] = $this->storeId;
        $data['webhookSettings'] = ['additionalWebhookUrls' => $this->webhookSettings->getAdditionalWebhookUrls()];
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
    public function getWebhookSettings(): WebhookSettings
    {
        return $this->webhookSettings;
    }
    public function setWebhookSettings(WebhookSettings $webhookSettings): void
    {
        $this->webhookSettings = $webhookSettings;
    }
}
