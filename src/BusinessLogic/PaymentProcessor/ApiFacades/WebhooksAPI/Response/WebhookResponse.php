<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\WebhooksAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
/**
 * Class WebhookResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\WebhooksAPI\Response
 */
class WebhookResponse extends Response
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [];
    }
}
