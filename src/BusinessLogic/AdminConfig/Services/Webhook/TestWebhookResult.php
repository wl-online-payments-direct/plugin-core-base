<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Webhook;

/**
 * Class TestWebhookResult
 *
 * Outcome of one standalone send-test run: one entry per configured additional webhook URL, in the
 * order they are configured. The URLs are independent targets, so one failing says nothing about the
 * others - which is why this is a list of outcomes and not a single verdict.
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Webhook
 */
class TestWebhookResult
{
    /**
     * @var TestWebhookUrlResult[]
     */
    private array $urlResults;
    /**
     * @param TestWebhookUrlResult[] $urlResults
     */
    public function __construct(array $urlResults)
    {
        $this->urlResults = $urlResults;
    }
    /**
     * @return TestWebhookUrlResult[]
     */
    public function getUrlResults(): array
    {
        return $this->urlResults;
    }
    /**
     * Whether every configured URL was sent to successfully. A run with no URLs never reaches this
     * class - TestWebhookService refuses it - so this cannot be a vacuous true.
     */
    public function isSuccessful(): bool
    {
        foreach ($this->urlResults as $urlResult) {
            if (!$urlResult->getOutcome()->isPassed()) {
                return \false;
            }
        }
        return \true;
    }
}
