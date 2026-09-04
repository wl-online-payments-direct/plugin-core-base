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
    /**
     * @param string $paymentProductId
     * @param TranslationCollection $name
     * @param string $paymentGroup
     * @param string[] $integrationTypes
     * @param bool $enabled
     */
    public function __construct(string $paymentProductId, TranslationCollection $name, string $paymentGroup, array $integrationTypes, bool $enabled)
    {
        $this->paymentProductId = $paymentProductId;
        $this->name = $name;
        $this->paymentGroup = $paymentGroup;
        $this->integrationTypes = $integrationTypes;
        $this->enabled = $enabled;
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
}
