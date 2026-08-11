<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Exceptions\BaseTranslatableException;
/**
 * Class NoAdditionalWebhookUrlsException
 *
 * Thrown when the standalone "Send Test Webhook" action is invoked for a store that has no additional
 * webhook URLs configured - either because none were ever added, or because the integration runs with
 * manual webhooks, where Core stores none at all. There is nothing to send a test to, so the action is
 * refused rather than answered with an empty, meaningless success.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions
 */
class NoAdditionalWebhookUrlsException extends BaseTranslatableException
{
}
