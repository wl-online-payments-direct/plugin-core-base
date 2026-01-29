<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Cards;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Exceptions\InvalidFlowTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class FlowType
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Cards
 */
class FlowType
{
    public const IFRAME = 'iframe';
    public const REDIRECT = 'redirect';
    protected string $type;
    /**
     * @param string $type
     */
    private function __construct(string $type)
    {
        $this->type = $type;
    }
    public static function iframe(): FlowType
    {
        return new self(self::IFRAME);
    }
    public static function redirect(): FlowType
    {
        return new self(self::REDIRECT);
    }
    /**
     * @param string $state
     *
     * @return FlowType
     *
     * @throws InvalidFlowTypeException
     */
    public static function fromState(string $state): FlowType
    {
        if ($state === self::IFRAME) {
            return new FlowType(self::IFRAME);
        }
        if ($state === self::REDIRECT) {
            return new FlowType(self::REDIRECT);
        }
        throw new InvalidFlowTypeException(new TranslatableLabel('Invalid flow type. Type type must be "iframe" or "redirect"."', 'payment.invalidFlowType'));
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
