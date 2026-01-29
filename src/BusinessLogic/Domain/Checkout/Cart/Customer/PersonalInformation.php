<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Customer;

/**
 * Class PersonalInformation.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Customer
 */
class PersonalInformation
{
    private string $firstName;
    private string $lastName;
    private string $gender;
    private string $title;
    public function __construct(string $firstName, string $lastName, string $gender = '', string $title = '')
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->gender = $gender;
        $this->title = $title;
    }
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    public function getLastName(): string
    {
        return $this->lastName;
    }
    public function getGender(): string
    {
        return $this->gender;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
}
