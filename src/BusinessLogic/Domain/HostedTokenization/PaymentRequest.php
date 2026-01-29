<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\CartProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\MemoryCachingCartProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\RoundingTotalsCartProvider;
/**
 * Class PaymentRequest.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization
 */
class PaymentRequest
{
    private string $hostedTokenizationId;
    private CartProvider $cartProvider;
    private string $returnUrl;
    private ?string $tokenId;
    public function __construct(string $hostedTokenizationId, CartProvider $cart, string $returnUrl, ?string $tokenId = null)
    {
        $this->hostedTokenizationId = $hostedTokenizationId;
        $this->cartProvider = new MemoryCachingCartProvider(new RoundingTotalsCartProvider($cart));
        $this->returnUrl = $returnUrl;
        $this->tokenId = $tokenId;
    }
    public function getHostedTokenizationId(): string
    {
        return $this->hostedTokenizationId;
    }
    public function getCartProvider(): CartProvider
    {
        return $this->cartProvider;
    }
    public function getReturnUrl(): string
    {
        return $this->returnUrl;
    }
    public function getTokenId(): ?string
    {
        return $this->tokenId;
    }
}
