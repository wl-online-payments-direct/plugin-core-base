<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData;

/**
 * Class Descriptor
 *
 * Per-method soft descriptor sent on the payment as order.references.descriptor. Configured on the
 * methods that support it (Sofinco, Linxo). An empty value leaves the descriptor unset.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData
 */
class Descriptor implements PaymentMethodAdditionalData
{
    /** Maximum length Worldline accepts for the descriptor. */
    public const MAX_LENGTH = 15;
    protected string $descriptor;
    /**
     * @param string $descriptor
     */
    public function __construct(string $descriptor)
    {
        $this->descriptor = substr($descriptor, 0, self::MAX_LENGTH);
    }
    /**
     * @return string
     */
    public function getDescriptor(): string
    {
        return $this->descriptor;
    }
}
