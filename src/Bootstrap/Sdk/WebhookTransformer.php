<?php

namespace WOP\OnlinePayments\Core\Bootstrap\Sdk;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ActiveConnectionProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Webhook\Transformers\WebhookTransformerInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Webhook\WebhookData;
use OnlinePayments\Sdk\Domain\WebhooksEvent;
use OnlinePayments\Sdk\Webhooks\InMemorySecretKeyStore;
use OnlinePayments\Sdk\Webhooks\WebhooksHelper;
/**
 * Class WebhookTransformer
 *
 * @package OnlinePayments\Core\Bootstrap\Sdk
 */
class WebhookTransformer implements WebhookTransformerInterface
{
    protected ActiveConnectionProvider $activeConnectionProvider;
    private const PAYMENT_LINK_WEBHOOK_TYPE = 'paymentlink.paid';
    /**
     * @param ActiveConnectionProvider $activeConnectionProvider
     */
    public function __construct(ActiveConnectionProvider $activeConnectionProvider)
    {
        $this->activeConnectionProvider = $activeConnectionProvider;
    }
    public function transform(string $webhookBody, array $requestHeaders): WebhookData
    {
        $sdkWebhook = $this->validate($webhookBody, $requestHeaders);
        return $this->doTransform($webhookBody, $sdkWebhook);
    }
    private function doTransform(string $webhookBody, WebhooksEvent $event): WebhookData
    {
        if ($event->type === self::PAYMENT_LINK_WEBHOOK_TYPE) {
            $arrayBody = json_decode($webhookBody, \true);
            if (!isset($arrayBody['paymentLink'])) {
                throw new \Exception('Payment link webhook failed. Error during request decoding.');
            }
            $paymentLinkArray = $arrayBody['paymentLink'];
            return new WebhookData($paymentLinkArray['paymentId'], $paymentLinkArray['paymentLinkOrder']['merchantReference'] ?: '', $event->type, $event->created, $paymentLinkArray['status'], StatusCode::incomplete()->getCode(), $webhookBody);
        }
        $response = $event->getPayment();
        $output = null;
        if ($response !== null) {
            $output = $response->getPaymentOutput();
        }
        if ($response === null) {
            $response = $event->getRefund();
            $output = $response ? $response->getRefundOutput() : null;
        }
        $status = $response->getStatusOutput();
        return new WebhookData($response->getId(), $output ? $output->getReferences()->getMerchantReference() : '', $event->type, $event->created, $status ? $status->getStatusCategory() : 'CREATED', $status ? $status->getStatusCode() : StatusCode::incomplete()->getCode(), $webhookBody);
    }
    private function validate(string $webhookBody, array $requestHeaders): WebhooksEvent
    {
        $connection = $this->activeConnectionProvider->get();
        $secretKeyStore = new InMemorySecretKeyStore([$connection->getActiveCredentials()->getWebhookKey() => $connection->getActiveCredentials()->getWebhookSecret()]);
        $helper = new WebhooksHelper($secretKeyStore);
        return $helper->unmarshal($webhookBody, $requestHeaders);
    }
}
