<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization;

/**
 * Class TokenResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization
 */
class TokenResponse
{
    private string $tokenId;
    private string $cardBrand;
    private string $cardNumber;
    private string $expirationDate;
    private string $logoUrl;
    /**
     * @param string $tokenId
     * @param string $cardBrand
     * @param string $cardNumber
     * @param string $expirationDate
     * @param string $logoUrl
     */
    public function __construct(string $tokenId, string $cardBrand, string $cardNumber, string $expirationDate, string $logoUrl = '')
    {
        $this->tokenId = $tokenId;
        $this->cardBrand = $cardBrand;
        $this->cardNumber = $cardNumber;
        $this->expirationDate = $expirationDate;
        $this->logoUrl = $logoUrl;
    }
    public function getTokenId(): string
    {
        return $this->tokenId;
    }
    public function getCardBrand(): string
    {
        return $this->cardBrand;
    }
    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }
    public function getExpirationDate(): string
    {
        return $this->expirationDate;
    }
    public function getLogoUrl(): string
    {
        return $this->logoUrl;
    }
}
