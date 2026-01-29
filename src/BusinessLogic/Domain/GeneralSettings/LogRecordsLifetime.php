<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidLogRecordsLifetimeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class LogRecordsLifetime
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings
 */
class LogRecordsLifetime
{
    protected int $days;
    private function __construct(int $days)
    {
        $this->days = $days;
    }
    /**
     * @param int $days
     *
     * @return LogRecordsLifetime
     *
     * @throws InvalidLogRecordsLifetimeException
     */
    public static function create(int $days): LogRecordsLifetime
    {
        if ($days < 1 || $days > 14) {
            throw new InvalidLogRecordsLifetimeException(new TranslatableLabel('Invalid logging records lifetime.', 'generalSettings.logRecordsLifetime.error'));
        }
        return new self($days);
    }
    public function getDays(): int
    {
        return $this->days;
    }
}
