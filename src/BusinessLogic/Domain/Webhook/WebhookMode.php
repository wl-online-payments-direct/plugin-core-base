<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Webhook;

use InvalidArgumentException;
/**
 * Class WebhookMode
 *
 * Integration wide webhook delivery mode. It is a property of the integration itself, not of a store or
 * a tenant, so it is set once from BootstrapComponent::bootstrap() and stays constant for the process.
 *
 * - MANUAL (default): the merchant registers the store's webhook URL in the Worldline back office. Core
 *   sends no webhook URLs with payment requests.
 * - AUTOMATIC: core sends the store's own webhook URL, together with any additional configured URLs, on
 *   every payment request, so Worldline delivers notifications without any back office configuration.
 *
 * Inbound webhook validation does not depend on the mode. It always verifies the payload signature with
 * the webhook key and secret of the active connection.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Webhook
 */
class WebhookMode
{
    public const MANUAL = 'manual';
    public const AUTOMATIC = 'automatic';
    private const SUPPORTED_MODES = [self::MANUAL, self::AUTOMATIC];
    /**
     * @var string
     */
    private static string $mode = self::MANUAL;
    private function __construct()
    {
    }
    /**
     * Sets the active mode. Called from bootstrap; integrations should not call it directly.
     *
     * @param string $mode One of the mode constants of this class.
     *
     * @return void
     *
     * @throws InvalidArgumentException When the given mode is not supported.
     */
    public static function set(string $mode): void
    {
        if (!in_array($mode, self::SUPPORTED_MODES, \true)) {
            throw new InvalidArgumentException(sprintf('Unsupported webhook mode "%s". Supported modes are: %s.', $mode, implode(', ', self::SUPPORTED_MODES)));
        }
        self::$mode = $mode;
    }
    /**
     * @return string
     */
    public static function get(): string
    {
        return self::$mode;
    }
    /**
     * @return bool
     */
    public static function isAutomatic(): bool
    {
        return self::$mode === self::AUTOMATIC;
    }
    /**
     * @return bool
     */
    public static function isManual(): bool
    {
        return self::$mode === self::MANUAL;
    }
}
