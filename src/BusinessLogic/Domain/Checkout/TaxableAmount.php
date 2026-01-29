<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Exceptions\CurrencyMismatchException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Exceptions\InvalidTaxRate;
/**
 * Class TaxableAmount
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Checkout
 */
class TaxableAmount
{
    /**
     * @var Amount
     */
    private Amount $amountExclTax;
    /**
     * @var Amount
     */
    private Amount $amountInclTax;
    /**
     * @var TaxRate
     */
    private TaxRate $taxRate;
    /**
     * @param Amount $amountExclTax
     * @param Amount $amountInclTax
     * @param TaxRate $taxRate
     */
    public function __construct(Amount $amountExclTax, Amount $amountInclTax, TaxRate $taxRate)
    {
        $this->amountExclTax = $amountExclTax;
        $this->amountInclTax = $amountInclTax;
        $this->taxRate = $taxRate;
    }
    /**
     * @param Amount $amountExlTax
     * @param Amount $amountInclTax
     *
     * @return TaxableAmount
     *
     * @throws InvalidTaxRate
     */
    public static function fromAmounts(Amount $amountExlTax, Amount $amountInclTax): TaxableAmount
    {
        $taxRate = new TaxRate(0);
        if ($amountInclTax->getValue() > 0 && $amountExlTax->getValue() > 0) {
            $taxRate = new TaxRate(($amountInclTax->getValue() / $amountExlTax->getValue() - 1) * 100);
        }
        return new self($amountExlTax, $amountInclTax, $taxRate);
    }
    /**
     * @param Amount $amountExlTax
     * @param TaxRate $taxRate
     *
     * @return TaxableAmount
     */
    public static function fromAmountExclTaxAndTaxRate(Amount $amountExlTax, TaxRate $taxRate): TaxableAmount
    {
        return new self($amountExlTax, Amount::fromFloat($amountExlTax->getPriceInCurrencyUnits() * (1 + $taxRate->getRate() / 100), $amountExlTax->getCurrency()), $taxRate);
    }
    /**
     * @param Amount $amountInclTax
     * @param TaxRate $taxPercentage
     *
     * @return TaxableAmount
     */
    public static function fromAmountInclTaxAndTaxRate(Amount $amountInclTax, TaxRate $taxPercentage): TaxableAmount
    {
        return new self(Amount::fromFloat($amountInclTax->getPriceInCurrencyUnits() / (1 + $taxPercentage->getRate() / 100), $amountInclTax->getCurrency()), $amountInclTax, $taxPercentage);
    }
    /**
     * @param Amount $amountExlTax
     *
     * @return TaxableAmount
     *
     * @throws InvalidTaxRate
     */
    public static function fromAmountExclTax(Amount $amountExlTax): TaxableAmount
    {
        return new self($amountExlTax, $amountExlTax, new TaxRate(0));
    }
    /**
     * @param Amount $amountInclTax
     *
     * @return TaxableAmount
     *
     * @throws InvalidTaxRate
     */
    public static function fromAmountInclTax(Amount $amountInclTax): TaxableAmount
    {
        return new self($amountInclTax, $amountInclTax, new TaxRate(0));
    }
    /**
     * @return Amount
     */
    public function getAmountExclTax(): Amount
    {
        return $this->amountExclTax;
    }
    /**
     * @return Amount
     */
    public function getAmountInclTax(): Amount
    {
        return $this->amountInclTax;
    }
    /**
     * @return TaxRate
     */
    public function getTaxRate(): TaxRate
    {
        return $this->taxRate;
    }
    /**
     * @return Amount
     *
     * @throws CurrencyMismatchException
     */
    public function getTaxAmount(): Amount
    {
        return $this->amountInclTax->minus($this->amountExclTax);
    }
    /**
     * Price without tax reduced by the price of the input parameter without tax
     * Price with tax reduced by the price of the input parameter with tax
     *
     * @param TaxableAmount $amount
     * @return TaxableAmount
     *
     * @throws CurrencyMismatchException
     * @throws InvalidTaxRate
     */
    public function minus(TaxableAmount $amount): TaxableAmount
    {
        return self::fromAmounts($this->amountExclTax->minus($amount->getAmountExclTax()), $this->amountInclTax->minus($amount->getAmountInclTax()));
    }
    /**
     * Price without tax increased by the price of the input parameter without tax
     * Price with tax increased by the price of the input parameter with tax
     *
     * @param TaxableAmount $amount
     * @return TaxableAmount
     *
     * @throws CurrencyMismatchException
     * @throws InvalidTaxRate
     */
    public function plus(TaxableAmount $amount): TaxableAmount
    {
        return self::fromAmounts($this->amountExclTax->plus($amount->getAmountExclTax()), $this->amountInclTax->plus($amount->getAmountInclTax()));
    }
    public function multiply(int $factor): TaxableAmount
    {
        return self::fromAmounts($this->amountExclTax->multiply($factor), $this->amountInclTax->multiply($factor));
    }
}
