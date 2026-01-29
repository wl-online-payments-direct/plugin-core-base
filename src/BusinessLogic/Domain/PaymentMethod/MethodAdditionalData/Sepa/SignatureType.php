<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Sepa;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidSignatureTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class SignatureType
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\RedirectPaymentMethod\Sepa
 */
class SignatureType
{
    public const SMS = 'SMS';
    public const UNSIGNED = 'UNSIGNED';
    public const TICK_BOX = 'TICK_BOX';
    protected string $type;
    /**
     * @param string $type
     */
    private function __construct(string $type)
    {
        $this->type = $type;
    }
    /**
     * @return self
     */
    public static function sms(): self
    {
        return new self(self::SMS);
    }
    /**
     * @return self
     */
    public static function unsigned(): self
    {
        return new self(self::UNSIGNED);
    }
    /**
     * @return self
     */
    public static function tickBox(): self
    {
        return new self(self::TICK_BOX);
    }
    /**
     * @param string $type
     *
     * @return self
     *
     * @throws InvalidSignatureTypeException
     */
    public static function parse(string $type): self
    {
        if ($type === self::SMS) {
            return new self(self::SMS);
        }
        if ($type === self::UNSIGNED) {
            return new self(self::UNSIGNED);
        }
        if ($type === self::TICK_BOX) {
            return new self(self::TICK_BOX);
        }
        throw new InvalidSignatureTypeException(new TranslatableLabel('Invalid signature type. Signature type must be "SMS", "UNSIGNED" or "TICK_BOX".', 'payment.invalidSignatureType'));
    }
    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
    /**
     * @param SignatureType $type
     * @return bool
     */
    public function equals(SignatureType $type): bool
    {
        return $this->type === $type->getType();
    }
}
