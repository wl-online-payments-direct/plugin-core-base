<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
/**
 * Class HostedCheckout
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData
 */
class HostedCheckout implements CarriesThreeDSSettings
{
    protected string $logo;
    protected bool $enableGroupCards;
    protected ThreeDSSettings $threeDSSettings;
    /**
     * @param string $logo
     * @param bool $enableGroupCards
     * @param ThreeDSSettings|null $threeDSSettings
     */
    public function __construct(string $logo, bool $enableGroupCards, ?ThreeDSSettings $threeDSSettings = null)
    {
        $this->logo = $logo;
        $this->enableGroupCards = $enableGroupCards;
        $this->threeDSSettings = $threeDSSettings ?: new ThreeDSSettings();
    }
    public function getLogo(): string
    {
        return $this->logo;
    }
    public function setLogo(string $logo): void
    {
        $this->logo = $logo;
    }
    public function isEnableGroupCards(): bool
    {
        return $this->enableGroupCards;
    }
    public function getThreeDSSettings(): ThreeDSSettings
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
