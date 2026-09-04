<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies;

use Exception;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Cart;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\HostedTokenization;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Token;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
/**
 * Interface HostedTokenizationProxyInterface.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies
 */
interface HostedTokenizationProxyInterface
{
    /**
     * @param Cart $cart
     * @param Token[] $savedTokens
     *
     * @return HostedTokenization
     */
    public function create(Cart $cart, array $savedTokens = [], ?PaymentProductId $productId = null, string $template = ''): HostedTokenization;
    /**
     * @param string $customerId
     * @param string $tokenId
     *
     * @return Token|null
     */
    public function getToken(string $customerId, string $tokenId): ?Token;
    /**
     * @param string $tokenId
     *
     * @return void
     *
     * @throws Exception
     */
    public function deleteToken(string $tokenId): void;
}
