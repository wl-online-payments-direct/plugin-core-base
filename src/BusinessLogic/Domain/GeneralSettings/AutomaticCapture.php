<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidAutomaticCaptureValueException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class AutomaticCapture
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings
 */
class AutomaticCapture
{
    protected const NEVER = -1;
    protected const ONE_HOUR = 60;
    protected const TWO_HOURS = 120;
    protected const FOUR_HOURS = 240;
    protected const EIGHT_HOURS = 480;
    protected const ONE_DAY = 1440;
    protected const TWO_DAYS = 2880;
    protected const FIVE_DAYS = 7200;
    protected int $value;
    private function __construct(int $value)
    {
        $this->value = $value;
    }
    public static function never(): self
    {
        return self::create(self::NEVER);
    }
    /**
     * @param int $value
     *
     * @return self
     *
     * @throws InvalidAutomaticCaptureValueException
     */
    public static function create(int $value): self
    {
        if (!in_array($value, [self::NEVER, self::ONE_HOUR, self::TWO_HOURS, self::FOUR_HOURS, self::EIGHT_HOURS, self::ONE_DAY, self::TWO_DAYS, self::FIVE_DAYS])) {
            throw new InvalidAutomaticCaptureValueException(new TranslatableLabel('Invalid automatic capture value ' . $value, 'generalSettings.automaticCaptureValue.error', [(string) $value]));
        }
        return new self($value);
    }
    public function equals(AutomaticCapture $automaticCapture): bool
    {
        return $this->value === $automaticCapture->getValue();
    }
    public function getValue(): int
    {
        return $this->value;
    }
}
