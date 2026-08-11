<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\PaymentMethodAdditionalData;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslationCollection;
/**
 * Class PaymentMethod.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod
 */
class PaymentMethod
{
    protected PaymentProductId $productId;
    protected TranslationCollection $name;
    protected bool $enabled;
    protected string $template;
    protected ?PaymentMethodAdditionalData $additionalData;
    protected ?PaymentAction $paymentAction = null;
    protected int $sortOrder;
    protected string $fallbackLocale;
    /**
     * @param PaymentProductId $productId
     * @param TranslationCollection $name
     * @param bool $enabled
     * @param string $template
     * @param PaymentMethodAdditionalData|null $additionalData
     * @param PaymentAction|null $paymentAction
     * @param int $sortOrder Merchant-configured display position at checkout, ascending. Methods never
     *  explicitly reordered default to 0 and fall back to static list order relative to one another.
     */
    public function __construct(PaymentProductId $productId, TranslationCollection $name, bool $enabled, string $template = '', ?PaymentMethodAdditionalData $additionalData = null, ?PaymentAction $paymentAction = null, int $sortOrder = 0, string $fallbackLocale = '')
    {
        $this->productId = $productId;
        $this->name = $name;
        $this->enabled = $enabled;
        $this->template = $template;
        $this->additionalData = $additionalData;
        $this->paymentAction = $paymentAction;
        $this->sortOrder = $sortOrder;
        $this->fallbackLocale = $fallbackLocale;
    }
    /**
     * @return PaymentProductId
     */
    public function getProductId(): PaymentProductId
    {
        return $this->productId;
    }
    /**
     * @return TranslationCollection
     */
    public function getName(): TranslationCollection
    {
        return $this->name;
    }
    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }
    /**
     * The locale this method falls back to when the shopper's own is not one Worldline supports.
     *
     * EMPTY means the method has not set one and inherits the store's `PaymentSettings` value - the
     * same "empty is unset" convention `getTemplate()` uses, and the reason this is a plain string
     * rather than a nullable: an empty locale is never a meaningful value, so there is nothing for the
     * two states to be confused about.
     *
     * Functional requirements: the locale "should also be available per PM", and the UI requirements
     * render (p6) shows the control in the per-method modal.
     */
    public function getFallbackLocale(): string
    {
        return $this->fallbackLocale;
    }
    /**
     * This method's locale if it set one, else the store-level fallback.
     */
    public function resolveFallbackLocale(string $storeFallbackLocale): string
    {
        return '' !== $this->fallbackLocale ? $this->fallbackLocale : $storeFallbackLocale;
    }
    public function getAdditionalData(): ?PaymentMethodAdditionalData
    {
        return $this->additionalData;
    }
    public function getPaymentAction(): ?PaymentAction
    {
        return $this->paymentAction;
    }
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }
}
