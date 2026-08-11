<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
/**
 * A 3DS block plus which level of the cascade it came from.
 *
 * The source is what lets the admin grey out inherited values honestly. Equality is not identity: a
 * method explicitly set to the same values as the store baseline is NOT inheriting, and a UI that
 * re-derived inheritance by comparing values would grey it out and then silently change it the day the
 * baseline moved.
 *
 * ONE source for the whole block, not one per field. ADR-0003 decision 10 sketched `{value, source}`
 * per field, but decision 5 resolves the block as a unit, so all five fields always share a source -
 * a per-field shape would be more granular than the model can express and would invite the UI to
 * render mixed states that cannot occur.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod
 */
class ResolvedThreeDSSettings
{
    /** The method the shopper chose carries its own configuration. */
    public const SOURCE_METHOD = 'method';
    /** A per-brand override inside Embedded Cards. */
    public const SOURCE_BRAND = 'brand';
    /** The store-level Default Settings baseline. */
    public const SOURCE_DEFAULT = 'default';
    /** Nothing was configured anywhere; ThreeDSSettings::hardened() applies. */
    public const SOURCE_HARDENED = 'hardened';
    private ThreeDSSettings $settings;
    private string $source;
    private function __construct(ThreeDSSettings $settings, string $source)
    {
        $this->settings = $settings;
        $this->source = $source;
    }
    public static function fromMethod(ThreeDSSettings $settings): ResolvedThreeDSSettings
    {
        return new self($settings, self::SOURCE_METHOD);
    }
    public static function fromBrandOverride(ThreeDSSettings $settings): ResolvedThreeDSSettings
    {
        return new self($settings, self::SOURCE_BRAND);
    }
    public static function fromDefaults(ThreeDSSettings $settings): ResolvedThreeDSSettings
    {
        return new self($settings, self::SOURCE_DEFAULT);
    }
    public static function hardened(): ResolvedThreeDSSettings
    {
        return new self(ThreeDSSettings::hardened(), self::SOURCE_HARDENED);
    }
    public function getSettings(): ThreeDSSettings
    {
        return $this->settings;
    }
    public function getSource(): string
    {
        return $this->source;
    }
    public function isInherited(): bool
    {
        return self::SOURCE_METHOD !== $this->source;
    }
    /**
     * The stricter of two resolved blocks, chosen ENTIRE (ADR-0003 decision 7).
     *
     * Never a per-field maximum: that would take `enforceStrongAuthentication` from one block and
     * `enable3dsExemption` from the other and produce a combination no stored row holds, which is
     * exactly the mixing decision 5 forbids.
     *
     * `exemptionType` is never compared - LOW_VALUE and TRANSACTION_RISK_ANALYSIS have no dominance
     * order, and by the time it could matter step 3 has already picked the block with exemptions off.
     */
    public static function stricter(ResolvedThreeDSSettings $first, ResolvedThreeDSSettings $second): ResolvedThreeDSSettings
    {
        $a = $first->getSettings();
        $b = $second->getSettings();
        if ($a->isEnable3ds() !== $b->isEnable3ds()) {
            return $a->isEnable3ds() ? $first : $second;
        }
        if ($a->isEnforceStrongAuthentication() !== $b->isEnforceStrongAuthentication()) {
            return $a->isEnforceStrongAuthentication() ? $first : $second;
        }
        if ($a->isEnable3dsExemption() !== $b->isEnable3dsExemption()) {
            return $a->isEnable3dsExemption() ? $second : $first;
        }
        if ($a->getExemptionLimit()->getValue() !== $b->getExemptionLimit()->getValue()) {
            return $a->getExemptionLimit()->getValue() < $b->getExemptionLimit()->getValue() ? $first : $second;
        }
        // Declared tiebreak, so the outcome never depends on lookup order.
        return $first;
    }
}
