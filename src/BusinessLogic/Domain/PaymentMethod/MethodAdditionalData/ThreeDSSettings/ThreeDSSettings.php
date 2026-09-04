<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Currency;
/**
 * Class ThreeDSSettings
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings
 */
class ThreeDSSettings
{
    protected bool $enable3ds;
    protected bool $enforceStrongAuthentication;
    protected bool $enable3dsExemption;
    protected ?ExemptionType $exemptionType = null;
    protected ?Amount $exemptionLimit = null;
    /**
     * @param bool $enable3ds
     * @param bool $enforceStrongAuthentication
     * @param bool $enable3dsExemption
     * @param ExemptionType|null $exemptionType
     * @param Amount|null $exemptionLimit
     */
    public function __construct(bool $enable3ds = \true, bool $enforceStrongAuthentication = \false, bool $enable3dsExemption = \false, ?ExemptionType $exemptionType = null, ?Amount $exemptionLimit = null)
    {
        $this->enable3ds = $enable3ds;
        $this->enforceStrongAuthentication = $enforceStrongAuthentication;
        $this->enable3dsExemption = $enable3dsExemption;
        $this->exemptionType = $exemptionType ?? ExemptionType::lowValue();
        $this->exemptionLimit = $exemptionLimit ?? Amount::fromInt(3000, Currency::fromIsoCode('EUR'));
    }
    /**
     * @return bool
     */
    public function isEnable3ds(): bool
    {
        return $this->enable3ds;
    }
    /**
     * @return bool
     */
    public function isEnforceStrongAuthentication(): bool
    {
        return $this->enforceStrongAuthentication;
    }
    /**
     * @return bool
     */
    public function isEnable3dsExemption(): bool
    {
        return $this->enable3dsExemption;
    }
    /**
     * @return ExemptionType|null
     */
    public function getExemptionType(): ?ExemptionType
    {
        return $this->exemptionType;
    }
    /**
     * @return Amount
     */
    public function getExemptionLimit(): Amount
    {
        return $this->exemptionLimit;
    }
}
