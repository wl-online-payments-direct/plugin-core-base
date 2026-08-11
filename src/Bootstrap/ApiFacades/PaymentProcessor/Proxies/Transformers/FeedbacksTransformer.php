<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories\WebhookSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Webhook\WebhookMode;
use WOP\OnlinePayments\Core\Infrastructure\Configuration\Configuration;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use OnlinePayments\Sdk\Domain\Feedbacks;
/**
 * Builds the SDK Feedbacks object attached to outgoing payment requests. With automatic webhooks the
 * store's own inbound webhook URL is sent so Worldline delivers status notifications without any back
 * office configuration, and any additional merchant-configured webhook URLs are appended.
 *
 * With manual webhooks nothing is sent, because the merchant registers the webhook URL in the Worldline
 * back office instead.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers
 */
class FeedbacksTransformer
{
    /**
     * @return Feedbacks|null Null when there is nothing to send, in which case callers must leave the
     *  feedbacks of the request unset.
     */
    public static function transform(): ?Feedbacks
    {
        if (!WebhookMode::isAutomatic()) {
            return null;
        }
        $urls = self::collectWebhookUrls();
        if (empty($urls)) {
            return null;
        }
        $feedbacks = new Feedbacks();
        $feedbacks->setWebhooksUrls($urls);
        return $feedbacks;
    }
    /**
     * The store's own webhook URL followed by the additional configured URLs, trimmed, de-duplicated
     * and with empties removed.
     *
     * @return string[]
     */
    private static function collectWebhookUrls(): array
    {
        /** @var Configuration $configuration */
        $configuration = ServiceRegister::getService(Configuration::CLASS_NAME);
        $urls = [$configuration->getWebhookUrl()];
        /** @var WebhookSettingsRepositoryInterface $webhookSettingsRepository */
        $webhookSettingsRepository = ServiceRegister::getService(WebhookSettingsRepositoryInterface::class);
        $webhookSettings = $webhookSettingsRepository->getWebhookSettings();
        if ($webhookSettings !== null) {
            $urls = array_merge($urls, $webhookSettings->getAdditionalWebhookUrls());
        }
        return array_values(array_unique(array_filter(array_map('trim', $urls))));
    }
}
