<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Intersolve;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidSessionTimeoutException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class SessionTimeout
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\RedirectPaymentMethod\Intersolve
 */
class SessionTimeout
{
    public const MIN_VALUE = 1;
    public const MAX_VALUE = 1440;
    /**
     * Session timeout is set in minutes
     * and must be between 1 and
     * 1440 (24 hours)
     *
     * @var int
     */
    protected int $duration;
    /**
     * @param int $duration
     *
     * @throws InvalidSessionTimeoutException
     */
    public function __construct(int $duration)
    {
        $this->validate($duration);
        $this->duration = $duration;
    }
    public function getDuration(): int
    {
        return $this->duration;
    }
    /**
     * @throws InvalidSessionTimeoutException
     */
    public function validate(int $duration): void
    {
        if ($duration < self::MIN_VALUE || $duration > self::MAX_VALUE) {
            throw new InvalidSessionTimeoutException(new TranslatableLabel('Invalid session timeout duration. Session timeout must be between ' . self::MIN_VALUE . ' and ' . self::MAX_VALUE, 'payments.sessionTimeoutDuration', [(string) self::MIN_VALUE, (string) self::MAX_VALUE]));
        }
    }
}
