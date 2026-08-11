<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\AdminConfig\Proxies;

use WOP\OnlinePayments\Core\Bootstrap\Sdk\MerchantClientFactory;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck\HealthCheckService;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck\Proxies\HealthCheckProxyInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionDetails;
use OnlinePayments\Sdk\Domain\SendTestRequest;
use OnlinePayments\Sdk\Domain\ValidateCredentialsRequest;
/**
 * Class HealthCheckProxy
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\AdminConfig\Proxies
 */
class HealthCheckProxy implements HealthCheckProxyInterface
{
    private const VALIDATION_RESULT_OK = 'OK';
    private MerchantClientFactory $clientFactory;
    public function __construct(MerchantClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }
    /**
     * @inheritDoc
     */
    public function isWebhookCredentialsValid(ConnectionDetails $connectionDetails): bool
    {
        $credentials = $connectionDetails->getActiveCredentials();
        $request = new ValidateCredentialsRequest();
        $request->setKey($credentials->getWebhookKey());
        $request->setSecret($credentials->getWebhookSecret());
        $client = $this->clientFactory->get($connectionDetails, HealthCheckService::REQUEST_TIMEOUT_SECONDS);
        return $client->webhooks()->validateWebhookCredentials($request)->getResult() === self::VALIDATION_RESULT_OK;
    }
    /**
     * @inheritDoc
     */
    public function sendTestWebhook(ConnectionDetails $connectionDetails, string $webhookUrl): void
    {
        $request = new SendTestRequest();
        $request->setUrl($webhookUrl);
        $client = $this->clientFactory->get($connectionDetails, HealthCheckService::REQUEST_TIMEOUT_SECONDS);
        $client->webhooks()->sendTestWebhook($request);
    }
}
