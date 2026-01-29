<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Intersolve;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidPaymentProductIdException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class PaymentProductId
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\RedirectPaymentMethod\Intersolve
 */
class PaymentProductId
{
    public const MIN_PRODUCT_ID = 5700;
    public const MAX_PRODUCT_ID = 5799;
    private string $id;
    private function __construct(string $id)
    {
        $this->id = $id;
    }
    /**
     * String representation of the payment method id
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->id;
    }
    /**
     * @throws InvalidPaymentProductIdException
     */
    public static function parse(string $id): PaymentProductId
    {
        if (!self::isSupported($id)) {
            throw new InvalidPaymentProductIdException(new TranslatableLabel(sprintf('Payment prodcut ID is invalid %s.', $id), 'paymentMethod.invalidProductId', [$id]));
        }
        return new self($id);
    }
    /**
     * Checks if Intersolve custom gift card id
     * is between 5700 and 5799.
     *
     * @param string $id
     *
     * @return bool
     */
    public static function isSupported(string $id): bool
    {
        return (int) $id >= self::MIN_PRODUCT_ID && (int) $id <= self::MAX_PRODUCT_ID;
    }
    public function equals(string $id): bool
    {
        return $this->id === $id;
    }
    public function getId(): string
    {
        return $this->id;
    }
}
