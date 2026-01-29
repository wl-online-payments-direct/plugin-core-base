<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
/**
 * Class GooglePay
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData
 */
class GooglePay implements PaymentMethodAdditionalData
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
}
