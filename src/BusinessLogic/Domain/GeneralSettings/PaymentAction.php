<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidActionTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class PaymentAction
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod
 */
class PaymentAction
{
    public const AUTHORIZE = 'FINAL_AUTHORIZATION';
    public const AUTHORIZE_CAPTURE = 'SALE';
    private string $type;
    /**
     * @param string $type
     */
    private function __construct(string $type)
    {
        $this->type = $type;
    }
    public static function authorize(): PaymentAction
    {
        return new self(self::AUTHORIZE);
    }
    public static function authorizeCapture(): PaymentAction
    {
        return new self(self::AUTHORIZE_CAPTURE);
    }
    /**
     * @param string $state
     *
     * @return PaymentAction
     *
     * @throws InvalidActionTypeException
     */
    public static function fromState(string $state): PaymentAction
    {
        if ($state === self::AUTHORIZE_CAPTURE) {
            return new self(self::AUTHORIZE_CAPTURE);
        }
        if ($state === self::AUTHORIZE) {
            return new self(self::AUTHORIZE);
        }
        throw new InvalidActionTypeException(new TranslatableLabel('Invalid action type. Action type must be "authorize" or "authorize-capture"', 'payment.invalidActionType'));
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function equals(PaymentAction $action): bool
    {
        return $this->type === $action->getType();
    }
}
