<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment;

/**
 * Class PaymentId.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Payment
 */
class PaymentId
{
    private const NEW_FORMAT_PREFIX = '90000';
    private const OLD_FORMAT_PREFIX = '900000';
    private const FORMAT_SUFFIX_LENGTH = 3;
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
        return \false === strpos($id, '_') && 0 === strpos($id, self::NEW_FORMAT_PREFIX);
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
     * Returns transaction id without trailing operations sequence indexes. Auto-detects the payment id
     * format by prefix and extracts the inner transaction id. Examples:
     * - "4365991440_0" returns "4365991440"
     * - "9000004375008553000" (old format, "900000" prefix) returns "4375008553"
     * - "9000099999999999000" (new format, "90000" prefix) returns "99999999999"
     *
     * @return string
     */
    public function getTransactionId(): string
    {
        if (\false !== strpos($this->id, '_')) {
            return (string) strstr($this->id, '_', \true);
        }
        if (0 === strpos($this->id, self::OLD_FORMAT_PREFIX)) {
            return substr($this->id, strlen(self::OLD_FORMAT_PREFIX), -self::FORMAT_SUFFIX_LENGTH);
        }
        return substr($this->id, strlen(self::NEW_FORMAT_PREFIX), -self::FORMAT_SUFFIX_LENGTH);
    }
    /**
     * Returns transaction id using the legacy "900000" prefix extraction. Used as a fallback when looking
     * up persisted transactions whose transactionId column was written by the previous extraction logic
     * that always assumed a 6-character prefix.
     *
     * @return string
     */
    public function getOldTransactionId(): string
    {
        if (\false !== strpos($this->id, '_')) {
            return (string) strstr($this->id, '_', \true);
        }
        return substr($this->id, strlen(self::OLD_FORMAT_PREFIX), -self::FORMAT_SUFFIX_LENGTH);
    }
}
