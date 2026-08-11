<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Webhook\TestWebhookResult;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Webhook\TestWebhookUrlResult;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
/**
 * Class SendTestWebhooksResponse
 *
 * `success` is true only when every configured URL was sent to successfully; `results` always carries
 * the per-URL outcome, so a partial run (some of the merchant's up-to-four URLs passed, others did
 * not) is reported in full rather than collapsed into one verdict. A partial run is still a 200 -
 * nothing went wrong with the request itself.
 *
 * The outcome values are the health check's own: `passed`, `failed` (Worldline answered KO - not
 * retried), `technicalError` (network/timeout/5xx, retried twice and still unresolved).
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\GeneralSettingsAPI\Response
 */
class SendTestWebhooksResponse extends Response
{
    private TestWebhookResult $result;
    public function __construct(TestWebhookResult $result)
    {
        $this->result = $result;
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return ['success' => $this->result->isSuccessful(), 'results' => array_map(static function (TestWebhookUrlResult $urlResult): array {
            return ['url' => $urlResult->getUrl(), 'outcome' => $urlResult->getOutcome()->getName()];
        }, $this->result->getUrlResults())];
    }
}
