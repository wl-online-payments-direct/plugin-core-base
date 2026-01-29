<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Webhook;

/**
 * Class WebhookData
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Webhook
 */
class WebhookData
{
    private string $id;
    private string $merchantReference;
    private string $type;
    private string $created;
    private string $statusCategory;
    private int $statusCode;
    private string $webhookBody;
    /**
     * @param string $id
     * @param string $merchantReference
     * @param string $type
     * @param string $created
     * @param string $statusCategory
     * @param int $statusCode
     * @param string $webhookBody
     */
    public function __construct(string $id, string $merchantReference, string $type, string $created, string $statusCategory, int $statusCode, string $webhookBody)
    {
        $this->id = $id;
        $this->merchantReference = $merchantReference;
        $this->type = $type;
        $this->created = $created;
        $this->statusCategory = $statusCategory;
        $this->statusCode = $statusCode;
        $this->webhookBody = $webhookBody;
    }
    public function getId(): string
    {
        return $this->id;
    }
    public function getMerchantReference(): string
    {
        return $this->merchantReference;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function getCreated(): string
    {
        return $this->created;
    }
    public function getStatusCategory(): string
    {
        return $this->statusCategory;
    }
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    public function getWebhookBody(): string
    {
        return $this->webhookBody;
    }
}
