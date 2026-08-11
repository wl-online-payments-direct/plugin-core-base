<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Repositories\PaymentSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\CarriesThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\CreditCard;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\Repositories\PaymentConfigRepositoryInterface;
/**
 * The one place that decides which 3DS configuration a payment is sent with.
 *
 * The cascade runs DOWNWARD THROUGH CONTAINMENT and never sideways between peers (ADR-0003):
 *
 *     Default Settings (store baseline)
 *             | unless overridden
 *     Per-method config   (Embedded Cards / Redirection to Cards / Visa / ...)
 *             | unless overridden        (Embedded Cards only)
 *     Per-brand override  (Visa inside the embedded form)
 *
 * The user's worked example, decided 2026-08-06: Embedded Cards with 3DS on and Visa allowed, while
 * the Visa payment method has 3DS off. A card typed into the embedded form IS authenticated; a payment
 * through the Visa button is NOT. Neither configuration overrides the other, because the method the
 * shopper chose is the one that applies.
 *
 * That is why context matters and a product id alone is not enough. `resolveForMethod()` answers "the
 * shopper chose this method"; `resolveWithinCardParent()` answers "the shopper chose this parent and
 * the card turned out to be this brand". Handing a brand id to the first would silently apply the
 * standalone Visa button's posture to a payment made inside the embedded form.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod
 */
class ThreeDSSettingsService
{
    protected PaymentConfigRepositoryInterface $paymentConfigRepository;
    protected PaymentSettingsRepositoryInterface $paymentSettingsRepository;
    /**
     * Memoised for the request. The baseline is read once per resolution chain and never per field;
     * the admin list resolves ~12 methods on a page that already fans out to the products API.
     *
     * @var ThreeDSSettings|null
     */
    private ?ThreeDSSettings $baseline = null;
    private bool $baselineLoaded = \false;
    public function __construct(PaymentConfigRepositoryInterface $paymentConfigRepository, PaymentSettingsRepositoryInterface $paymentSettingsRepository)
    {
        $this->paymentConfigRepository = $paymentConfigRepository;
        $this->paymentSettingsRepository = $paymentSettingsRepository;
    }
    /**
     * The 3DS settings a payment on this product must be sent with, or null for products that have no
     * 3DS configuration at all.
     *
     * @return ThreeDSSettings|null
     */
    public function getThreeDSSettings(PaymentProductId $paymentProductId): ?ThreeDSSettings
    {
        $resolved = $this->resolveForMethod($paymentProductId);
        return $resolved ? $resolved->getSettings() : null;
    }
    /**
     * Resolves for a method the shopper chose directly, reporting which level answered.
     *
     * @return ResolvedThreeDSSettings|null Null for products that carry no 3DS configuration.
     */
    public function resolveForMethod(PaymentProductId $paymentProductId): ?ResolvedThreeDSSettings
    {
        if (!$this->carriesThreeDSConfiguration($paymentProductId)) {
            return null;
        }
        if ($paymentProductId->isCardBrand()) {
            return $this->resolveForBrand($paymentProductId);
        }
        return $this->resolveOwnConfigOrBaseline($paymentProductId->getId());
    }
    /**
     * Resolves inside a grouped card flow: the parent the shopper chose, overridden by that parent's
     * own per-brand override when the card turns out to be one of the brands it overrides.
     *
     * The brand's OWN payment method is deliberately not consulted. It is a peer, not a container - the
     * standalone Visa button and the Visa card typed into the embedded form are different choices by
     * the shopper and carry different configuration.
     *
     * @param string|null $brandId The brand the card resolved to, when known.
     */
    public function resolveWithinCardParent(PaymentProductId $parentId, ?string $brandId = null): ResolvedThreeDSSettings
    {
        $parentConfig = $this->cardConfigFor($parentId->getId());
        if (null !== $brandId && $parentConfig) {
            $override = $parentConfig->getBrandThreeDSOverride($brandId);
            if ($override) {
                return ResolvedThreeDSSettings::fromBrandOverride($override);
            }
        }
        return $this->resolveOwnConfigOrBaseline($parentId->getId());
    }
    /**
     * A card product was requested that the store is not currently offering.
     *
     * The caller owns this judgement because it knows the OFFERED set - enabled, available for this
     * cart, and surviving grouping - which is strictly narrower than "the row is enabled". Preserving
     * that check is what keeps the 49a5d10 fix intact: an unoffered method cannot be the one being paid
     * with, so its stored posture gets no say.
     */
    public function resolveForUnofferedCardRequest(PaymentProductId $requested): ResolvedThreeDSSettings
    {
        if ($requested->isCardBrand()) {
            return $this->resolveFromOfferingParents($requested->getId());
        }
        // An unoffered method that is not a brand has no container to fall back to.
        return ResolvedThreeDSSettings::hardened();
    }
    /**
     * A brand requested as a payment method in its own right.
     *
     * When that brand is not actually offered - a disabled row, or no row at all - the request cannot
     * be honoured on the brand's own terms, and the brand's stored configuration must get no say: that
     * is the SCA bypass fixed in 49a5d10, where a switched-off brand carrying a laxer posture could be
     * used to weaken a payment. It falls back to the posture of a parent that DOES offer the brand
     * (ADR-0003 decision 7), which is the configuration the store actually presented.
     */
    private function resolveForBrand(PaymentProductId $brandId): ResolvedThreeDSSettings
    {
        $brandMethod = $this->paymentConfigRepository->getPaymentMethod($brandId->getId());
        if ($brandMethod && $brandMethod->isEnabled()) {
            return $this->resolveOwnConfigOrBaseline($brandId->getId());
        }
        return $this->resolveFromOfferingParents($brandId->getId());
    }
    /**
     * The enabled card parents that allow this brand, resolved and reduced to the strictest.
     *
     * Both parents allowing every brand is the DEFAULT state, not a corner case - functional
     * requirements p11 adds all brands to both lists by default - so this path is ordinary traffic and
     * the strictness order is load-bearing.
     */
    private function resolveFromOfferingParents(string $brandId): ResolvedThreeDSSettings
    {
        $candidates = [];
        foreach (PaymentProductId::cardParentIds() as $parentId) {
            $parentMethod = $this->paymentConfigRepository->getPaymentMethod($parentId);
            if (!$parentMethod || !$parentMethod->isEnabled()) {
                continue;
            }
            $additionalData = $parentMethod->getAdditionalData();
            if (!$additionalData instanceof CreditCard || !$additionalData->allowsBrand($brandId)) {
                continue;
            }
            $candidates[] = $this->resolveWithinCardParent(PaymentProductId::parse($parentId), $brandId);
        }
        if (empty($candidates)) {
            // No enabled method offers this brand at all, so the request is genuinely anomalous. Only
            // here does the hardened constant apply rather than the merchant's configuration.
            return ResolvedThreeDSSettings::hardened();
        }
        return array_reduce($candidates, static function (?ResolvedThreeDSSettings $carry, ResolvedThreeDSSettings $candidate) {
            return null === $carry ? $candidate : ResolvedThreeDSSettings::stricter($carry, $candidate);
        });
    }
    /**
     * The method's own block if it set one, else the store baseline, else the hardened constant.
     *
     * A method that set nothing inherits the baseline DOWNWARD; it never borrows from a peer method.
     */
    private function resolveOwnConfigOrBaseline(string $productId): ResolvedThreeDSSettings
    {
        $config = $this->paymentConfigRepository->getPaymentMethod($productId);
        $additionalData = $config ? $config->getAdditionalData() : null;
        if ($additionalData instanceof CarriesThreeDSSettings && $additionalData->hasThreeDSSettings()) {
            return ResolvedThreeDSSettings::fromMethod($additionalData->getThreeDSSettings());
        }
        $baseline = $this->getBaseline();
        return $baseline ? ResolvedThreeDSSettings::fromDefaults($baseline) : ResolvedThreeDSSettings::hardened();
    }
    /**
     * The card configuration of a method, or null when it has none.
     */
    private function cardConfigFor(string $productId): ?CreditCard
    {
        $config = $this->paymentConfigRepository->getPaymentMethod($productId);
        $additionalData = $config ? $config->getAdditionalData() : null;
        return $additionalData instanceof CreditCard ? $additionalData : null;
    }
    /**
     * The store-level baseline, or null when the merchant never configured one.
     *
     * Null is not the same as `new ThreeDSSettings()`: keeping them apart is what lets "nothing set
     * anywhere" reach the hardened constant instead of silently inheriting that constructor's lax
     * `enforceStrongAuthentication = false` (ADR-0003 decisions 4 and 6).
     */
    private function getBaseline(): ?ThreeDSSettings
    {
        if ($this->baselineLoaded) {
            return $this->baseline;
        }
        $paymentSettings = $this->paymentSettingsRepository->getPaymentSettings();
        $this->baseline = $paymentSettings ? $paymentSettings->getDefaultMethodSettings()->getThreeDSSettings() : null;
        $this->baselineLoaded = \true;
        return $this->baseline;
    }
    private function carriesThreeDSConfiguration(PaymentProductId $paymentProductId): bool
    {
        return PaymentProductId::googlePay()->equals($paymentProductId->getId()) || PaymentProductId::hostedCheckout()->equals($paymentProductId->getId()) || PaymentProductId::payByLink()->equals($paymentProductId->getId()) || $paymentProductId->isCardType();
    }
}
