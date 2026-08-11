<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidSessionTimeoutException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class SessionTimeout
 *
 * How long, in minutes, the hosted payment page session should last. Customers attempting to complete
 * their payment past this limit will not be allowed to finish the transaction.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings
 */
class SessionTimeout
{
    private const MIN_MINUTES = 1;
    private const MAX_MINUTES = 1440;
    protected int $minutes;
    private function __construct(int $minutes)
    {
        $this->minutes = $minutes;
    }
    /**
     * @param int $minutes
     *
     * @return SessionTimeout
     *
     * @throws InvalidSessionTimeoutException
     */
    public static function create(int $minutes): SessionTimeout
    {
        if ($minutes < self::MIN_MINUTES || $minutes > self::MAX_MINUTES) {
            throw new InvalidSessionTimeoutException(new TranslatableLabel('Invalid session timeout.', 'generalSettings.sessionTimeout.error'));
        }
        return new self($minutes);
    }
    public function getMinutes(): int
    {
        return $this->minutes;
    }
}
