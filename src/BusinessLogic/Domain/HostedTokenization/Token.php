<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization;

/**
 * Class Token.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization
 */
class Token
{
    private string $customerId;
    private string $tokenId;
    private string $productId;
    private string $cardNumber;
    private string $expiryDate;
    public function __construct(string $customerId, string $tokenId, string $productId, string $cardNumber, string $expiryDate)
    {
        $this->customerId = $customerId;
        $this->tokenId = $tokenId;
        $this->productId = $productId;
        $this->cardNumber = $cardNumber;
        $this->expiryDate = $expiryDate;
    }
    public function getCustomerId(): string
    {
        return $this->customerId;
    }
    public function getTokenId(): string
    {
        return $this->tokenId;
    }
    public function getProductId(): string
    {
        return $this->productId;
    }
    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }
    public function getExpiryDate(): string
    {
        return $this->expiryDate;
    }
}
