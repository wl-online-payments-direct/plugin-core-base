<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
/**
 * The store-level Default Settings baseline: the settings a payment method uses unless it overrides
 * them (ADR-0003).
 *
 * It is ONE value object rather than three fields on `PaymentSettings` because that aggregate's
 * constructor is already positional and `php >= 7.4` has no named arguments, so every extra parameter
 * is another chance for a caller to get the order wrong.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings
 */
class DefaultMethodSettings
{
    /**
     * NULL means the merchant has never configured a store-level 3DS baseline, which is not the same
     * as configuring `new ThreeDSSettings()`. Keeping the two apart is what lets the cascade treat
     * "nothing set at any level" as its hardened constant instead of silently inheriting
     * `ThreeDSSettings`' lax `enforceStrongAuthentication = false` default (ADR-0003 decision 6).
     *
     * @var ThreeDSSettings|null
     */
    protected ?ThreeDSSettings $threeDSSettings;
    /**
     * Overridden per method by `PaymentMethod::getTemplate()` for methods on the hosted-page flow.
     * Empty means unset.
     *
     * @var string
     */
    protected string $templateIdHostedCheckout;
    /**
     * As above, for the embedded/iframe flow.
     *
     * @var string
     */
    protected string $templateIdEmbeddedCheckout;
    /**
     * @param ThreeDSSettings|null $threeDSSettings
     * @param string $templateIdHostedCheckout
     * @param string $templateIdEmbeddedCheckout
     */
    public function __construct(?ThreeDSSettings $threeDSSettings = null, string $templateIdHostedCheckout = '', string $templateIdEmbeddedCheckout = '')
    {
        $this->threeDSSettings = $threeDSSettings;
        $this->templateIdHostedCheckout = $templateIdHostedCheckout;
        $this->templateIdEmbeddedCheckout = $templateIdEmbeddedCheckout;
    }
    /**
     * @return ThreeDSSettings|null NULL when no store-level baseline has been configured.
     */
    public function getThreeDSSettings(): ?ThreeDSSettings
    {
        return $this->threeDSSettings;
    }
    /**
     * @return string
     */
    public function getTemplateIdHostedCheckout(): string
    {
        return $this->templateIdHostedCheckout;
    }
    /**
     * @return string
     */
    public function getTemplateIdEmbeddedCheckout(): string
    {
        return $this->templateIdEmbeddedCheckout;
    }
}
