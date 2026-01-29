<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Exceptions\InvalidTaxRate;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
/**
 * Class TaxRate
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Checkout
 */
class TaxRate
{
    /**
     * @var float
     */
    private float $taxRatePercentage;
    /**
     * @param float $taxRatePercentage
     * @throws InvalidTaxRate
     */
    public function __construct(float $taxRatePercentage)
    {
        if ($taxRatePercentage < 0 || $taxRatePercentage >= 100) {
            throw new InvalidTaxRate(new TranslatableLabel('Tax rate should be between 0 and 100 percents.', 'checkout.taxError'));
        }
        $this->taxRatePercentage = $taxRatePercentage;
    }
    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->taxRatePercentage;
    }
    public function __toString(): string
    {
        return number_format($this->taxRatePercentage, 6);
    }
}
