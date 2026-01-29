<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidExemptionTypeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class ExemptionType
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\CreditCard
 */
class ExemptionType
{
    public const LOW_VALUE = 'low-value';
    public const TRANSACTION_RISK_ANALYSIS = 'transaction-risk-analysis';
    protected string $type;
    /**
     * @param string $type
     */
    private function __construct(string $type)
    {
        $this->type = $type;
    }
    public static function lowValue(): self
    {
        return new self(self::LOW_VALUE);
    }
    public static function transactionRiskAnalysis(): self
    {
        return new self(self::TRANSACTION_RISK_ANALYSIS);
    }
    /**
     * @param string $state
     *
     * @return ExemptionType
     *
     * @throws InvalidExemptionTypeException
     */
    public static function fromState(string $state): ExemptionType
    {
        if ($state === self::TRANSACTION_RISK_ANALYSIS) {
            return new self(self::TRANSACTION_RISK_ANALYSIS);
        }
        if ($state === self::LOW_VALUE) {
            return new self(self::LOW_VALUE);
        }
        throw new InvalidExemptionTypeException(new TranslatableLabel('Invalid exemption type. Exemption type must be "low-value" or "transaction-risk-analysis"', 'payment.invalidExemptionType'));
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function equals(self $other): bool
    {
        return $this->type === $other->getType();
    }
}
