<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Exceptions\BaseTranslatableException;
/**
 * Class WebhookSettingsNotSupportedException
 *
 * Thrown when webhook settings are saved on an integration running with manual webhooks, where there is
 * nowhere to store them and nothing would send them. Reporting this is better than accepting the values
 * and silently dropping them.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions
 */
class WebhookSettingsNotSupportedException extends BaseTranslatableException
{
}
