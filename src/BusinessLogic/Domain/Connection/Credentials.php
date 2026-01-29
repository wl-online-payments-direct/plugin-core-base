<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection;

/**
 * Class Credentials.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Connection
 */
class Credentials
{
    private string $pspId;
    private string $apiKey;
    private string $apiSecret;
    private string $webhookKey;
    private string $webhookSecret;
    /**
     * @param string $pspId
     * @param string $apiKey
     * @param string $apiSecret
     * @param string $webhookKey
     * @param string $webhookSecret
     */
    public function __construct(string $pspId, string $apiKey, string $apiSecret, string $webhookKey, string $webhookSecret)
    {
        $this->pspId = $pspId;
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->webhookKey = $webhookKey;
        $this->webhookSecret = $webhookSecret;
    }
    /**
     * @return string
     */
    public function getPspId(): string
    {
        return $this->pspId;
    }
    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }
    /**
     * @return string
     */
    public function getApiSecret(): string
    {
        return $this->apiSecret;
    }
    /**
     * @return string
     */
    public function getWebhookKey(): string
    {
        return $this->webhookKey;
    }
    /**
     * @return string
     */
    public function getWebhookSecret(): string
    {
        return $this->webhookSecret;
    }
}
