<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Cards\FlowType;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslationCollection;
/**
 * Class CreditCard
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData
 */
class CreditCard implements PaymentMethodAdditionalData
{
    protected ?TranslationCollection $vaultTitles;
    protected ?ThreeDSSettings $threeDSSettings;
    protected FlowType $type;
    protected bool $enableGroupCards;
    /**
     * @param TranslationCollection|null $vaultTitles
     * @param ThreeDSSettings|null $threeDSSettings
     * @param FlowType|null $type
     * @param bool $enableGroupCards
     */
    public function __construct(?TranslationCollection $vaultTitles, ?ThreeDSSettings $threeDSSettings = null, FlowType $type = null, bool $enableGroupCards = \true)
    {
        $this->vaultTitles = $vaultTitles;
        $this->threeDSSettings = $threeDSSettings ?: new ThreeDSSettings();
        $this->type = $type ?: FlowType::iframe();
        $this->enableGroupCards = $enableGroupCards;
    }
    /**
     * @return TranslationCollection|null
     */
    public function getVaultTitles(): ?TranslationCollection
    {
        return $this->vaultTitles;
    }
    public function getThreeDSSettings(): ?ThreeDSSettings
    {
        return $this->threeDSSettings;
    }
    public function getType(): FlowType
    {
        return $this->type;
    }
    public function isEnableGroupCards(): bool
    {
        return $this->enableGroupCards;
    }
}
