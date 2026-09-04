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
    /**
     * @param PaymentProductId $productId
     * @param TranslationCollection $name
     * @param bool $enabled
     * @param string $template
     * @param PaymentMethodAdditionalData|null $additionalData
     * @param PaymentAction|null $paymentAction
     */
    public function __construct(PaymentProductId $productId, TranslationCollection $name, bool $enabled, string $template = '', ?PaymentMethodAdditionalData $additionalData = null, ?PaymentAction $paymentAction = null)
    {
        $this->productId = $productId;
        $this->name = $name;
        $this->enabled = $enabled;
        $this->template = $template;
        $this->additionalData = $additionalData;
        $this->paymentAction = $paymentAction;
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
    public function getAdditionalData(): ?PaymentMethodAdditionalData
    {
        return $this->additionalData;
    }
    public function getPaymentAction(): ?PaymentAction
    {
        return $this->paymentAction;
    }
}
