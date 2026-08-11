<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Request\Request;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidWebhookUrlException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\WebhookSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class WebhookSettingsRequest
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Request
 */
class WebhookSettingsRequest extends Request
{
    /**
     * @var string[]
     */
    protected array $additionalWebhookUrls;
    /**
     * @param string[] $additionalWebhookUrls
     */
    public function __construct(array $additionalWebhookUrls)
    {
        $this->additionalWebhookUrls = $additionalWebhookUrls;
    }
    /**
     * @inheritDoc
     *
     * @throws InvalidWebhookUrlException When a non-empty URL is not a valid https:// URL.
     */
    public function transformToDomainModel(): object
    {
        $urls = array_values(array_filter(array_map('trim', $this->additionalWebhookUrls)));
        foreach ($urls as $url) {
            if (!WebhookSettings::isValidWebhookUrl($url)) {
                throw new InvalidWebhookUrlException(new TranslatableLabel(sprintf('Invalid webhook URL "%s". It must be a valid URL starting with "https://" and ' . 'include a proper hostname (e.g. https://example.com).', $url), 'generalSettings.webhookSettings.invalidUrl'));
            }
        }
        return new WebhookSettings($urls);
    }
}
