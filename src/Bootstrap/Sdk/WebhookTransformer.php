<?php

namespace WOP\OnlinePayments\Core\Bootstrap\Sdk;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ActiveConnectionProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Webhook\Exceptions\WebhookMerchantMismatchException;
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
        $arrayBody = json_decode($webhookBody, \true) ?: [];
        // Payment webhooks carry a top-level "type" (which the SDK maps to WebhooksEvent::$type),
        // but payment-link webhooks use "eventType" instead, leaving $event->type null. Resolve the
        // event type from either field so both kinds of webhook are recognised.
        $eventType = $arrayBody['eventType'] ?? $arrayBody['type'] ?? $event->type;
        if ($eventType === self::PAYMENT_LINK_WEBHOOK_TYPE) {
            if (!isset($arrayBody['paymentLink'])) {
                throw new \Exception('Payment link webhook failed. Error during request decoding.');
            }
            $paymentLinkArray = $arrayBody['paymentLink'];
            return new WebhookData($paymentLinkArray['paymentId'] ?? '', $paymentLinkArray['paymentLinkOrder']['merchantReference'] ?: '', self::PAYMENT_LINK_WEBHOOK_TYPE, $event->created, $paymentLinkArray['status'], StatusCode::incomplete()->getCode(), $webhookBody);
        }
        $response = $event->getPayment();
        $output = $response ? $response->getPaymentOutput() : null;
        if ($response === null) {
            $response = $event->getRefund();
            $output = $response ? $response->getRefundOutput() : null;
        }
        if ($response === null) {
            // The event carries neither a payment nor a refund (e.g. payout, token, or a non-paid
            // payment-link event). There is nothing to reconcile, so return a neutral payload the
            // WebhookService safely ignores instead of dereferencing null.
            return new WebhookData('', '', (string) $eventType, (string) $event->created, 'CREATED', StatusCode::incomplete()->getCode(), $webhookBody);
        }
        $status = $response->getStatusOutput();
        return new WebhookData($response->getId(), $output ? $output->getReferences()->getMerchantReference() : '', (string) $eventType, $event->created, $status ? $status->getStatusCategory() : 'CREATED', $status ? $status->getStatusCode() : StatusCode::incomplete()->getCode(), $webhookBody);
    }
    private function validate(string $webhookBody, array $requestHeaders): WebhooksEvent
    {
        $connection = $this->activeConnectionProvider->get();
        $credentials = $connection->getActiveCredentials();
        $secretKeyStore = new InMemorySecretKeyStore([$credentials->getWebhookKey() => $credentials->getWebhookSecret()]);
        $helper = new WebhooksHelper($secretKeyStore);
        $event = $helper->unmarshal($webhookBody, $requestHeaders);
        $this->validateMerchantId($webhookBody, $credentials->getPspId());
        return $event;
    }
    /**
     * Ensures the merchant the webhook was issued for matches the merchant (pspId) configured for the
     * current store. Without this check a payload that passes signature verification could still be
     * processed against a store it was not intended for.
     *
     * @throws WebhookMerchantMismatchException
     */
    private function validateMerchantId(string $webhookBody, string $pspId): void
    {
        $body = json_decode($webhookBody, \true);
        $merchantId = is_array($body) && isset($body['merchantId']) ? (string) $body['merchantId'] : '';
        if ('' === $merchantId || !hash_equals($pspId, $merchantId)) {
            throw new WebhookMerchantMismatchException(new TranslatableLabel('Webhook merchant id does not match the configured merchant.', 'webhook.merchantIdMismatch'));
        }
    }
}
