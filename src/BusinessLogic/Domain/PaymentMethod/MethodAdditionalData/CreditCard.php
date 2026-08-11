<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Cards\AuthorizationMode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Cards\FlowType;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslationCollection;
/**
 * Class CreditCard
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData
 */
class CreditCard implements CarriesThreeDSSettings
{
    protected ?TranslationCollection $vaultTitles;
    protected ?ThreeDSSettings $threeDSSettings;
    /**
     * Whether the 3DS block above is the merchant's own configuration or a materialised stand-in.
     *
     * Out-of-band rather than a sentinel inside the block, per ADR-0003 decision 4: `src/` has no
     * `declare(strict_types=1)` anywhere, so a string sentinel reaching `ThreeDSSettings`' three plain
     * `bool` parameters would coerce silently - and `"inherit"` coerces to `true`, which on
     * `enable3dsExemption` means exemptions ON. Fail-open, with no error.
     */
    protected bool $threeDSSettingsSet;
    protected FlowType $type;
    protected bool $enableGroupCards;
    protected AuthorizationMode $authorizationMode;
    /** @var string[] */
    protected array $allowedBrands;
    /**
     * Per-brand 3DS overrides, keyed by brand product id. Embedded Cards only.
     *
     * A key's PRESENCE is what "set" means here. That is a third representation beside the two in
     * ADR-0003 decision 4, and it is safe for the same reason the store level's nullable property is:
     * there is no sentinel value to coerce. The method level needs an out-of-band boolean because its
     * block is always serialised in full, so absence carries no information there; this map only ever
     * holds the entries the merchant actually made.
     *
     * @var ThreeDSSettings[]
     */
    protected array $brandThreeDSOverrides;
    /**
     * @param TranslationCollection|null $vaultTitles
     * @param ThreeDSSettings|null $threeDSSettings
     * @param FlowType|null $type
     * @param bool $enableGroupCards
     * @param AuthorizationMode|null $authorizationMode
     * @param string[]|null $allowedBrands Null means every brand, which is the documented default;
     *     an EMPTY array means the merchant removed them all, which is a different thing.
     * @param ThreeDSSettings[] $brandThreeDSOverrides Keyed by brand product id; only brands the
     *     merchant explicitly overrode appear.
     * @param bool|null $threeDSSettingsSet Whether the 3DS block is the merchant's own or a stand-in.
     *     Null derives it from whether a block was passed, which is right for every caller except the
     *     persistence layer, where the stored bit is authoritative.
     */
    public function __construct(?TranslationCollection $vaultTitles, ?ThreeDSSettings $threeDSSettings = null, FlowType $type = null, bool $enableGroupCards = \true, ?AuthorizationMode $authorizationMode = null, ?array $allowedBrands = null, array $brandThreeDSOverrides = [], ?bool $threeDSSettingsSet = null)
    {
        $this->vaultTitles = $vaultTitles;
        // The block is still materialised when absent, so the many unguarded getThreeDSSettings()
        // callers keep working. What the cascade needs is the separate bit below - whether that block
        // is anything the merchant chose.
        $this->threeDSSettings = $threeDSSettings ?: new ThreeDSSettings();
        $this->threeDSSettingsSet = $threeDSSettingsSet ?? null !== $threeDSSettings;
        $this->type = $type ?: FlowType::iframe();
        $this->enableGroupCards = $enableGroupCards;
        $this->authorizationMode = $authorizationMode ?: AuthorizationMode::finalAuthorization();
        $this->allowedBrands = null === $allowedBrands ? PaymentProductId::CARD_BRANDS : self::sanitiseBrands($allowedBrands);
        $this->brandThreeDSOverrides = $this->sanitiseOverrides($brandThreeDSOverrides);
    }
    /**
     * Keeps only overrides for brands this method actually offers.
     *
     * Two reasons to drop the rest rather than store them. An override for a brand outside the allowed
     * list is configuration checkout will never consult, so the admin would be showing the merchant a
     * setting with no effect. And keeping it means narrowing the allowed list quietly parks an
     * override that reappears, unreviewed, the day the brand is added back.
     *
     * @param ThreeDSSettings[] $overrides
     *
     * @return ThreeDSSettings[]
     */
    private function sanitiseOverrides(array $overrides): array
    {
        $kept = [];
        foreach ($this->allowedBrands as $brand) {
            if (isset($overrides[$brand]) && $overrides[$brand] instanceof ThreeDSSettings) {
                $kept[$brand] = $overrides[$brand];
            }
        }
        return $kept;
    }
    /**
     * @return ThreeDSSettings[] Keyed by brand product id.
     */
    public function getBrandThreeDSOverrides(): array
    {
        return $this->brandThreeDSOverrides;
    }
    /**
     * @return ThreeDSSettings|null NULL when this brand has no override, i.e. it inherits.
     */
    public function getBrandThreeDSOverride(string $brandId): ?ThreeDSSettings
    {
        return $this->brandThreeDSOverrides[$brandId] ?? null;
    }
    /**
     * Keeps only real card brands, in CARD_BRANDS order, without duplicates.
     *
     * The list is merchant-supplied and its destination is a Worldline product filter, where entries
     * are cast to integers. An id that is not a brand would therefore restrict the checkout to some
     * unrelated product - or, for the non-numeric ids, to product 0.
     *
     * @param string[] $brands
     *
     * @return string[]
     */
    private static function sanitiseBrands(array $brands): array
    {
        return array_values(array_filter(PaymentProductId::CARD_BRANDS, static function (string $brand) use ($brands): bool {
            return in_array($brand, $brands, \true);
        }));
    }
    /**
     * The card brands this method offers. Functional requirements p11 puts an "Allowed brands"
     * multiselect on both card parents, "all added by default".
     *
     * Individual card methods carry this field too, because they share the credit-card configuration
     * shape, but nothing reads it for them - a brand method offers exactly itself.
     *
     * @return string[]
     */
    public function getAllowedBrands(): array
    {
        return $this->allowedBrands;
    }
    public function allowsBrand(string $brandId): bool
    {
        return in_array($brandId, $this->allowedBrands, \true);
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
    /**
     * Whether this method carries a 3DS configuration of its own. False means it inherits, and the
     * block `getThreeDSSettings()` returns is a stand-in that must not be sent to Worldline.
     */
    public function hasThreeDSSettings(): bool
    {
        return $this->threeDSSettingsSet;
    }
    public function getType(): FlowType
    {
        return $this->type;
    }
    public function isEnableGroupCards(): bool
    {
        return $this->enableGroupCards;
    }
    public function getAuthorizationMode(): AuthorizationMode
    {
        return $this->authorizationMode;
    }
}
