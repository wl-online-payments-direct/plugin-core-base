<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Webhook;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\GeneralSettingsService;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck\HealthCheckService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Exceptions\InvalidConnectionDetailsException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Repositories\ConnectionConfigRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\NoAdditionalWebhookUrlsException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class TestWebhookService
 *
 * Backs the standalone "Send Test Webhook" action on the Webhook Notifications page (WBS S1, UI
 * requirements p8): it sends one mock webhook event to each of the store's CONFIGURED additional
 * webhook URLs.
 *
 * It owns no sending and no retry logic of its own. Each URL goes through
 * HealthCheckService::sendTestWebhook() - the same call, with the same retry policy, that the
 * save-time three-step check uses for its sendTest step (functional requirements 1: the health-check
 * logic is one reusable service and is not to be re-written).
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Webhook
 */
class TestWebhookService
{
    private ConnectionConfigRepositoryInterface $connectionConfigRepository;
    private GeneralSettingsService $generalSettingsService;
    private HealthCheckService $healthCheckService;
    public function __construct(ConnectionConfigRepositoryInterface $connectionConfigRepository, GeneralSettingsService $generalSettingsService, HealthCheckService $healthCheckService)
    {
        $this->connectionConfigRepository = $connectionConfigRepository;
        $this->generalSettingsService = $generalSettingsService;
        $this->healthCheckService = $healthCheckService;
    }
    /**
     * Sends a test webhook to every configured additional webhook URL and reports the outcome of each.
     *
     * The URLs are independent endpoints, so one failing does not stop the rest: every URL is attempted
     * and the caller gets the full picture. The store's own webhook URL is deliberately NOT included -
     * this action belongs to the "Your Additional Webhook URLs" section and tests exactly what that
     * section lists.
     *
     * @return TestWebhookResult
     *
     * @throws InvalidConnectionDetailsException When no connection has been saved for this store yet.
     * @throws NoAdditionalWebhookUrlsException When the store has no additional webhook URLs to test.
     */
    public function sendTestWebhooks(): TestWebhookResult
    {
        $connectionDetails = $this->connectionConfigRepository->getConnection();
        if (null === $connectionDetails) {
            throw new InvalidConnectionDetailsException(new TranslatableLabel('No saved connection to check.', 'connection.noSavedConnection'));
        }
        $urls = $this->generalSettingsService->getWebhookSettings()->getAdditionalWebhookUrls();
        if (empty($urls)) {
            throw new NoAdditionalWebhookUrlsException(new TranslatableLabel('No additional webhook URLs are configured, so there is nothing to send a test webhook to.', 'generalSettings.webhookSettings.noUrlsConfigured'));
        }
        $urlResults = [];
        foreach ($urls as $url) {
            $urlResults[] = new TestWebhookUrlResult($url, $this->healthCheckService->sendTestWebhook($connectionDetails, $url));
        }
        return new TestWebhookResult($urlResults);
    }
}
