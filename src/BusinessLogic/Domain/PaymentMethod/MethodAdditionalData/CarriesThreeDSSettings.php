<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
/**
 * Additional-data shapes that hold a 3DS block: the card methods, the hosted page, Google Pay and
 * Pay by Link.
 *
 * It exists so the cascade has one seam to ask rather than probing for methods on
 * `PaymentMethodAdditionalData`, which declares neither. The distinction it adds over a plain getter
 * is `hasThreeDSSettings()`: the cascade has to tell a block the merchant configured from one that was
 * materialised to keep unguarded callers working, and only the first may be sent to Worldline.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData
 */
interface CarriesThreeDSSettings extends PaymentMethodAdditionalData
{
    public function getThreeDSSettings(): ?ThreeDSSettings;
    /**
     * @return bool False means this method inherits, and its block is a stand-in.
     */
    public function hasThreeDSSettings(): bool;
}
