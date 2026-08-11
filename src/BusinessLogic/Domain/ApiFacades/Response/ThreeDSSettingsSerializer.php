<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
/**
 * The 3DS block as the admin API exposes it, in one place so its consumers cannot drift apart: the
 * four per-method branches (cards, hosted checkout, Google Pay, Pay by Link) and the store-level
 * Default Settings baseline.
 *
 * Distinct from `ThreeDSSettings::toArray()`, which is the persisted shape - here the exemption limit
 * is a plain amount in currency units rather than a value/currency pair.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response
 */
class ThreeDSSettingsSerializer
{
    /**
     * @param ThreeDSSettings $threeDSSettings
     *
     * @return array
     */
    public static function toArray(ThreeDSSettings $threeDSSettings): array
    {
        return ['enable3ds' => $threeDSSettings->isEnable3ds(), 'enforceStrongAuthentication' => $threeDSSettings->isEnforceStrongAuthentication(), 'enable3dsExemption' => $threeDSSettings->isEnable3dsExemption(), 'exemptionType' => $threeDSSettings->getExemptionType()->getType(), 'exemptionLimit' => $threeDSSettings->getExemptionLimit()->getPriceInCurrencyUnits()];
    }
    /**
     * A map of per-brand overrides in the same shape, keyed by brand product id.
     *
     * @param ThreeDSSettings[] $overrides
     *
     * @return array<string, array>
     */
    public static function mapToArray(array $overrides): array
    {
        $serialised = [];
        foreach ($overrides as $brandId => $settings) {
            // (string) because PHP has already turned these numeric brand ids into integer keys, and
            // the admin UI matches them against string product ids.
            $serialised[(string) $brandId] = self::toArray($settings);
        }
        return $serialised;
    }
}
