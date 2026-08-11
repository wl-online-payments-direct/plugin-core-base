<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Cards;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidAuthorizationModeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class AuthorizationMode
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Cards
 */
class AuthorizationMode
{
    public const FINAL_AUTHORIZATION = 'FINAL_AUTHORIZATION';
    public const PRE_AUTHORIZATION = 'PRE_AUTHORIZATION';
    protected string $type;
    /**
     * @param string $type
     */
    private function __construct(string $type)
    {
        $this->type = $type;
    }
    public static function finalAuthorization(): AuthorizationMode
    {
        return new self(self::FINAL_AUTHORIZATION);
    }
    public static function preAuthorization(): AuthorizationMode
    {
        return new self(self::PRE_AUTHORIZATION);
    }
    /**
     * @param string $state
     *
     * @return AuthorizationMode
     *
     * @throws InvalidAuthorizationModeException
     */
    public static function fromState(string $state): AuthorizationMode
    {
        if ($state === self::FINAL_AUTHORIZATION) {
            return new self(self::FINAL_AUTHORIZATION);
        }
        if ($state === self::PRE_AUTHORIZATION) {
            return new self(self::PRE_AUTHORIZATION);
        }
        throw new InvalidAuthorizationModeException(new TranslatableLabel('Invalid authorization mode. Mode must be "FINAL_AUTHORIZATION" or "PRE_AUTHORIZATION".', 'payment.invalidAuthorizationMode'));
    }
    public function equals(self $other): bool
    {
        return $this->type === $other->getType();
    }
    public function getType(): string
    {
        return $this->type;
    }
}
