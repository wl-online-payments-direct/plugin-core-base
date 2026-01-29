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
}
