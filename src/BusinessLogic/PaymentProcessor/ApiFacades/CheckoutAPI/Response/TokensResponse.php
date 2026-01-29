<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Response;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\ApiFacades\Response\Response;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\TokenResponse;
/**
 * Class TokensResponse
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\ApiFacades\CheckoutAPI\Response
 */
class TokensResponse extends Response
{
    /**
     * @var TokenResponse[]
     */
    private array $tokens;
    /**
     * @param TokenResponse[] $tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->tokens as $token) {
            $result[] = ['tokenId' => $token->getTokenId(), 'cardBrand' => $token->getCardBrand(), 'cardNumber' => $token->getCardNumber(), 'expirationDate' => preg_replace('/(\d{2})(\d{2})/', '$1/$2', $token->getExpirationDate()), 'logoUrl' => $token->getLogoUrl()];
        }
        return $result;
    }
}
