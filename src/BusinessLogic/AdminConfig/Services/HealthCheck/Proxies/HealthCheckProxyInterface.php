<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck\Proxies;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionDetails;
/**
 * Interface HealthCheckProxyInterface
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck\Proxies
 */
interface HealthCheckProxyInterface
{
    /**
     * Validates that the given webhook key/secret are accepted by Worldline.
     *
     * @param ConnectionDetails $connectionDetails
     *
     * @return bool
     */
    public function isWebhookCredentialsValid(ConnectionDetails $connectionDetails): bool;
    /**
     * Asks Worldline to send a mock webhook event to the given URL, to confirm the merchant's server can
     * receive it. Throws if the request itself fails; a successful call does not mean delivery to
     * $webhookUrl succeeded, only that Worldline accepted the request to attempt it.
     *
     * @param ConnectionDetails $connectionDetails
     * @param string $webhookUrl
     *
     * @return void
     */
    public function sendTestWebhook(ConnectionDetails $connectionDetails, string $webhookUrl): void;
}
