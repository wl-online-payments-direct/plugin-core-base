<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Currency;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Exceptions\InvalidCurrencyCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidExemptionTypeException;
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
     * The posture used when NOTHING is configured at any level of the cascade (ADR-0003 decision 6):
     * 3DS on, strong authentication enforced, no exemptions.
     *
     * Deliberately not `new ThreeDSSettings()`, whose constructor defaults
     * `enforceStrongAuthentication` to FALSE. Before the cascade, the stricter behaviour came from the
     * more complete row and the empty one was laxer; a cascade makes empty intermediate levels normal,
     * which would move that asymmetry from an edge case onto the hot path.
     */
    public static function hardened(): ThreeDSSettings
    {
        return new self(\true, \true, \false);
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
    /**
     * The persisted shape, shared by every store that carries a 3DS block - per-method rows and the
     * store-level baseline alike - so the two cannot drift apart. Follows `Amount`'s precedent of the
     * value object owning its own array form.
     *
     * @return array
     */
    public function toArray(): array
    {
        return ['enable3ds' => $this->enable3ds, 'enforceStrongAuthentication' => $this->enforceStrongAuthentication, 'enable3dsExemption' => $this->enable3dsExemption, 'exemptionType' => $this->exemptionType ? $this->exemptionType->getType() : '', 'exemptionLimit' => $this->exemptionLimit ? $this->exemptionLimit->toArray() : ''];
    }
    /**
     * @param array $data
     *
     * @return ThreeDSSettings
     *
     * @throws InvalidCurrencyCode
     * @throws InvalidExemptionTypeException
     */
    public static function fromArray(array $data): ThreeDSSettings
    {
        return new self(
            $data['enable3ds'] ?? \true,
            // Fails CLOSED, like the two defaults around it. An absent key means a partial or
            // older-schema row, not a merchant decision - and reading it as `false` silently dropped
            // the forced challenge with no log. Before card brands became configurable the bare array
            // access threw a TypeError here, so this state used to be loud.
            $data['enforceStrongAuthentication'] ?? \true,
            $data['enable3dsExemption'] ?? \false,
            !empty($data['exemptionType']) ? ExemptionType::fromState($data['exemptionType']) : null,
            !empty($data['exemptionLimit']) ? Amount::fromArray($data['exemptionLimit']) : null
        );
    }
}
