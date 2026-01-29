<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Sepa;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidRecurrenceTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class RecurrenceType
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\RedirectPaymentMethod\Sepa
 */
class RecurrenceType
{
    public const UNIQUE = 'unique';
    public const RECURRING = 'recurring';
    protected string $type;
    /**
     * @param string $type
     */
    private function __construct(string $type)
    {
        $this->type = $type;
    }
    public static function unique(): self
    {
        return new self(self::UNIQUE);
    }
    public static function recurring(): self
    {
        return new self(self::RECURRING);
    }
    /**
     * @param string $type
     *
     * @return self
     *
     * @throws InvalidRecurrenceTypeException
     */
    public static function parse(string $type): self
    {
        if ($type === self::UNIQUE) {
            return new self(self::UNIQUE);
        }
        if ($type === self::RECURRING) {
            return new self(self::RECURRING);
        }
        throw new InvalidRecurrenceTypeException(new TranslatableLabel('Invalid recurrence type. Recurrence type must be "unique" or "recurring"', 'payment.invalidRecurrenceType'));
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function equals(RecurrenceType $type): bool
    {
        return $this->type === $type->getType();
    }
}
