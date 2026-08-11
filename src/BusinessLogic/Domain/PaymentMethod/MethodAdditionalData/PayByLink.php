<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\Exceptions\InvalidPayByLinkExpirationTimeException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PayByLinkExpirationTime;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
/**
 * Class PayByLink
 *
 * Pay by Link's configuration per the V2 functional requirements (p11): "The configuration is the same
 * as for Redirection to Worldline payment method, with the next modification: Remove the Upload logo
 * field. Add the Default expiration time field." - i.e. HostedCheckout without the logo, plus the
 * default link expiration.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData
 */
class PayByLink implements CarriesThreeDSSettings
{
    /** Days a link stays valid when the merchant has not configured a value. */
    public const DEFAULT_EXPIRATION_DAYS = 7;
    protected PayByLinkExpirationTime $expirationTime;
    protected bool $enableGroupCards;
    protected ThreeDSSettings $threeDSSettings;
    /**
     * @param PayByLinkExpirationTime|null $expirationTime
     * @param bool $enableGroupCards
     * @param ThreeDSSettings|null $threeDSSettings
     *
     * @throws InvalidPayByLinkExpirationTimeException
     */
    public function __construct(?PayByLinkExpirationTime $expirationTime = null, bool $enableGroupCards = \true, ?ThreeDSSettings $threeDSSettings = null)
    {
        $this->expirationTime = $expirationTime ?: PayByLinkExpirationTime::create(self::DEFAULT_EXPIRATION_DAYS);
        $this->enableGroupCards = $enableGroupCards;
        $this->threeDSSettings = $threeDSSettings ?: new ThreeDSSettings();
    }
    public function getExpirationTime(): PayByLinkExpirationTime
    {
        return $this->expirationTime;
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
