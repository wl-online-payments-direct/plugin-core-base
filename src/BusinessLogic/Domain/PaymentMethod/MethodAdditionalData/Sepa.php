<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Sepa\RecurrenceType;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Sepa\SignatureType;
/**
 * Class Sepa
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\RedirectPaymentMethod
 */
class Sepa implements PaymentMethodAdditionalData
{
    protected ?RecurrenceType $recurrenceType = null;
    protected ?SignatureType $signatureType = null;
    /**
     * @param RecurrenceType|null $recurrenceType
     * @param SignatureType|null $signatureType
     */
    public function __construct(?RecurrenceType $recurrenceType = null, ?SignatureType $signatureType = null)
    {
        $this->recurrenceType = $recurrenceType;
        $this->signatureType = $signatureType;
    }
    public function getRecurrenceType(): ?RecurrenceType
    {
        return $this->recurrenceType;
    }
    public function getSignatureType(): ?SignatureType
    {
        return $this->signatureType;
    }
}
