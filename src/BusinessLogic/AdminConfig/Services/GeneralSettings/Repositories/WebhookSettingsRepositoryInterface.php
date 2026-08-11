<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\WebhookSettings;
/**
 * Interface WebhookSettingsRepositoryInterface
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories
 */
interface WebhookSettingsRepositoryInterface
{
    /**
     * @return WebhookSettings|null
     */
    public function getWebhookSettings(): ?WebhookSettings;
    /**
     * @param WebhookSettings $webhookSettings
     *
     * @return void
     */
    public function saveWebhookSettings(WebhookSettings $webhookSettings): void;
}
