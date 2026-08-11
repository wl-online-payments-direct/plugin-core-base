<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslationCollection;
/**
 * Class PaymentMethodResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod
 */
class PaymentMethodResponse
{
    protected string $paymentProductId;
    protected TranslationCollection $name;
    protected string $paymentGroup;
    /**
     * @var string[]
     */
    protected array $integrationTypes;
    protected bool $enabled;
    protected ?string $flowType;
    /**
     * @param string $paymentProductId
     * @param TranslationCollection $name
     * @param string $paymentGroup
     * @param string[] $integrationTypes
     * @param bool $enabled
     * @param string|null $flowType The flow the merchant CONFIGURED for this method, where it is
     *     merchant-settable at all. Null for every method that has no such setting. Distinct from
     *     $integrationTypes, which is the catalogue's list of flows the product SUPPORTS - a card
     *     brand supports several and is configured as exactly one, and the admin list labels it by
     *     the one it is configured as.
     */
    public function __construct(string $paymentProductId, TranslationCollection $name, string $paymentGroup, array $integrationTypes, bool $enabled, ?string $flowType = null)
    {
        $this->paymentProductId = $paymentProductId;
        $this->name = $name;
        $this->paymentGroup = $paymentGroup;
        $this->integrationTypes = $integrationTypes;
        $this->enabled = $enabled;
        $this->flowType = $flowType;
    }
    public function getPaymentProductId(): string
    {
        return $this->paymentProductId;
    }
    public function getName(): TranslationCollection
    {
        return $this->name;
    }
    public function getPaymentGroup(): string
    {
        return $this->paymentGroup;
    }
    public function getIntegrationTypes(): array
    {
        return $this->integrationTypes;
    }
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
    public function getFlowType(): ?string
    {
        return $this->flowType;
    }
}
