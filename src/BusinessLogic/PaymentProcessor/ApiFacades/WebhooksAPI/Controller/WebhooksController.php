<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\WebhooksAPI\Controller;

use Exception;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Monitoring\WebhookLogsService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Webhook\Transformers\WebhookTransformerInterface;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\WebhooksAPI\Response\WebhookResponse;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\Webhooks\WebhookService;
/**
 * Class WebhooksController
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\WebhooksAPI\Controller
 */
class WebhooksController
{
    protected WebhookTransformerInterface $transformer;
    protected WebhookService $webhookService;
    protected WebhookLogsService $webhookLogsService;
    /**
     * @param WebhookTransformerInterface $transformer
     * @param WebhookService $webhookService
     * @param WebhookLogsService $webhookLogsService
     */
    public function __construct(WebhookTransformerInterface $transformer, WebhookService $webhookService, WebhookLogsService $webhookLogsService)
    {
        $this->transformer = $transformer;
        $this->webhookService = $webhookService;
        $this->webhookLogsService = $webhookLogsService;
    }
    /**
     * @param string $webhookBody
     * @param array $requestHeaders
     *
     * @return WebhookResponse
     *
     * @throws Exception
     */
    public function process(string $webhookBody, array $requestHeaders): WebhookResponse
    {
        $webhook = $this->transformer->transform($webhookBody, $requestHeaders);
        $this->webhookLogsService->logWebhook($webhook);
        $this->webhookService->process($webhook);
        return new WebhookResponse();
    }
}
