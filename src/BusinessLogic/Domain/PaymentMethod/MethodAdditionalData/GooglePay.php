<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
/**
 * Class GooglePay
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData
 */
class GooglePay implements CarriesThreeDSSettings
{
    protected ?ThreeDSSettings $threeDSSettings;
    /**
     * @param ThreeDSSettings|null $threeDSSettings
     */
    public function __construct(?ThreeDSSettings $threeDSSettings = null)
    {
        $this->threeDSSettings = $threeDSSettings ?: new ThreeDSSettings();
    }
    public function getThreeDSSettings(): ?ThreeDSSettings
    {
        return $this->threeDSSettings;
    }
    /**
     * Always the merchant's own: this shape has no inherit state, because the method carries a 3DS
     * block only once it has been configured.
     */
    public function hasThreeDSSettings(): bool
    {
        return null !== $this->getThreeDSSettings();
    }
}
