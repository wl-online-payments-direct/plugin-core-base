<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment;

/**
 * Class PaymentId.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Payment
 */
class PaymentId
{
    private const TRANSACTION_ID_STANDARD_LENGTH = 10;
    private string $id;
    private function __construct(string $id)
    {
        $this->id = $id;
    }
    public static function parse(string $id): PaymentId
    {
        if (\false === strpos($id, '_') && !self::isNewPaymentIdFormat($id)) {
            return new self($id . '_0');
        }
        return new self($id);
    }
    private static function isNewPaymentIdFormat(string $id): bool
    {
        return \false === strpos($id, '_') && strlen($id) > self::TRANSACTION_ID_STANDARD_LENGTH;
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
     * Returns transaction id without trailing operations sequence indexes.
     * Example "4365991440" will be returned for "4365991440_0" payment id. Or for the new payment id format
     * for "4375008553" will be returned for "9000004375008553000" payment id
     *
     * @return string
     */
    public function getTransactionId(): string
    {
        if (self::isNewPaymentIdFormat($this->id)) {
            return substr($this->id, 6, self::TRANSACTION_ID_STANDARD_LENGTH);
        }
        return (string) strstr((string) $this, '_', \true);
    }
}
