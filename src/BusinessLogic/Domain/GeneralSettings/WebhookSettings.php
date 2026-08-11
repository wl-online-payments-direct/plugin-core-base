<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings;

/**
 * Class WebhookSettings
 *
 * Global (store-wide) webhook configuration. Holds the optional additional webhook URLs that are sent,
 * alongside the store's own webhook URL, in the payment `feedbacks` object.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings
 */
class WebhookSettings
{
    /**
     * @var string[]
     */
    protected array $additionalWebhookUrls;
    /**
     * @param string[] $additionalWebhookUrls
     */
    public function __construct(array $additionalWebhookUrls = [])
    {
        $this->additionalWebhookUrls = array_values(array_filter(array_map('trim', $additionalWebhookUrls)));
    }
    /**
     * @return string[]
     */
    public function getAdditionalWebhookUrls(): array
    {
        return $this->additionalWebhookUrls;
    }
    /**
     * Whether a webhook URL is acceptable: a syntactically valid URL served over https:// with a real
     * hostname (contains a dot and a non-empty top-level segment). Empty strings should be filtered out
     * before calling this.
     *
     * @param string $url
     *
     * @return bool
     */
    public static function isValidWebhookUrl(string $url): bool
    {
        if (filter_var($url, \FILTER_VALIDATE_URL) === \false) {
            return \false;
        }
        $parts = parse_url($url);
        if (empty($parts['scheme']) || strtolower($parts['scheme']) !== 'https') {
            return \false;
        }
        if (empty($parts['host']) || strpos($parts['host'], '.') === \false) {
            return \false;
        }
        $segments = explode('.', $parts['host']);
        return strlen((string) end($segments)) >= 1;
    }
}
