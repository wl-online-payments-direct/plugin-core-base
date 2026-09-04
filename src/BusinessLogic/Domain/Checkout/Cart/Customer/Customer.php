<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Customer;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Address;
/**
 * Class Customer.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart
 */
class Customer
{
    public const SUPPORTED_LOCALES = ['ar_AE', 'ca_ES', 'zh_CN', 'hr_HR', 'cs_CZ', 'da_DK', 'nl_NL', 'nl_BE', 'et_EE', 'en_GB', 'en_US', 'fi_FI', 'fr_FR', 'de_DE', 'de_AT', 'de_CH', 'el_GR', 'he_IL', 'hu_HU', 'it_IT', 'ja_JP', 'ko_KR', 'lt_LT', 'lv_LV', 'no_NO', 'pl_PL', 'pt_PT', 'ro_RO', 'ru_RU', 'sk_SK', 'sl_SI', 'es_ES', 'sv_SE', 'tr_TR'];
    private ContactDetails $contactDetails;
    private Address $billingAddress;
    private bool $isGuest;
    private string $merchantCustomerId;
    private string $locale;
    private ?Device $device;
    public function __construct(ContactDetails $contactDetails, Address $billingAddress, string $merchantCustomerId = '', bool $isGuest = \false, string $locale = 'en_GB', ?Device $device = null)
    {
        $this->contactDetails = $contactDetails;
        $this->billingAddress = $billingAddress;
        $this->isGuest = $isGuest;
        $this->merchantCustomerId = $merchantCustomerId;
        $this->locale = $locale;
        $this->device = $device;
    }
    public function getContactDetails(): ContactDetails
    {
        return $this->contactDetails;
    }
    public function getBillingAddress(): Address
    {
        return $this->billingAddress;
    }
    public function isGuest(): bool
    {
        return $this->isGuest;
    }
    public function getMerchantCustomerId(): string
    {
        return $this->merchantCustomerId;
    }
    public function getLocale(): string
    {
        return $this->locale;
    }
    public function setIsGuest(bool $isGuest): void
    {
        $this->isGuest = $isGuest;
    }
    public function setMerchantCustomerId(string $merchantCustomerId): void
    {
        $this->merchantCustomerId = $merchantCustomerId;
    }
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }
    public function getDevice(): ?Device
    {
        return $this->device;
    }
    public function setDevice(?Device $device): void
    {
        $this->device = $device;
    }
    public function getFormattedLocale(): string
    {
        if (in_array($this->locale, self::SUPPORTED_LOCALES, \true)) {
            return $this->locale;
        }
        $normalized = $this->locale;
        if (strpos($this->locale, '-') !== \false) {
            $normalized = str_replace('-', '_', $this->locale);
        }
        if (in_array($normalized, self::SUPPORTED_LOCALES, \true)) {
            return $normalized;
        }
        return $this->findLocaleByLanguage($normalized);
    }
    private function findLocaleByLanguage(string $language): string
    {
        $language = substr($language, 0, 2);
        foreach (self::SUPPORTED_LOCALES as $locale) {
            if (strpos($locale, $language . '_') === 0) {
                return $locale;
            }
        }
        return 'en_GB';
    }
}
