<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment;

/**
 * Class statusCode.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Payment
 */
class StatusCode
{
    public const PENDING_STATUS_CODES = [0, 4, 46, 50, 51, 52, 55, 91, 92, 99];
    public const CANCELLATION_STATUS_CODES = [1, 2, 6];
    public const REFUND_STATUS_CODES = [7, 8, 85];
    public const REFUND_REQUESTED_STATUS_CODES = [81, 82];
    public const CAPTURE_STATUS_CODES = [9];
    public const CAPTURE_REQUESTED_STATUS_CODES = [4, 91, 92, 99];
    public const CANCEL_STATUS_CODES = [6, 61, 62];
    private int $code;
    private function __construct(int $code)
    {
        $this->code = $code;
    }
    public static function parse(int $code): StatusCode
    {
        return new self($code);
    }
    public static function incomplete(): StatusCode
    {
        return new self(0);
    }
    public static function authorized(): StatusCode
    {
        return new self(5);
    }
    public static function completed(): StatusCode
    {
        return new self(9);
    }
    /**
     * String representation of the status code
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->code;
    }
    public function equals(StatusCode $other): bool
    {
        return $this->code === $other->getCode();
    }
    public function getCode(): int
    {
        return $this->code;
    }
    public function isPending(): bool
    {
        return in_array($this->code, self::PENDING_STATUS_CODES, \true);
    }
    public function isCanceledOrRejected(): bool
    {
        return in_array($this->code, self::CANCELLATION_STATUS_CODES, \true);
    }
    public function isRefunded(): bool
    {
        return in_array($this->code, self::REFUND_STATUS_CODES, \true);
    }
}
