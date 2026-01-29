<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Customer\PersonalInformation;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Country;
/**
 * Class Address.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart
 */
class Address
{
    private Country $country;
    private string $state;
    private string $city;
    private string $zip;
    private string $street;
    private string $houseNumber;
    private ?PersonalInformation $personalInformation;
    private string $companyName;
    private string $additionalInfo;
    public function __construct(Country $country, string $state, string $city, string $zip, string $street, string $houseNumber, PersonalInformation $personalInformation = null, string $companyName = '', string $additionalInfo = '')
    {
        $this->country = $country;
        $this->city = $city;
        $this->zip = $zip;
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->additionalInfo = $additionalInfo;
        $this->state = $state;
        $this->personalInformation = $personalInformation;
        $this->companyName = $companyName;
    }
    public function getCountry(): Country
    {
        return $this->country;
    }
    public function getState(): string
    {
        return $this->state;
    }
    public function getCity(): string
    {
        return $this->city;
    }
    public function getZip(): string
    {
        return $this->zip;
    }
    public function getStreet(): string
    {
        return $this->street;
    }
    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }
    public function getPersonalInformation(): ?PersonalInformation
    {
        return $this->personalInformation;
    }
    public function getCompanyName(): string
    {
        return $this->companyName;
    }
    public function getAdditionalInfo(): string
    {
        return $this->additionalInfo;
    }
}
