<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers\CreateHostedTokenizationRequestTransformer;
use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers\CreateHostedTokenizationResponseTransformer;
use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers\TokenResponseTransformer;
use WOP\OnlinePayments\Core\Bootstrap\Sdk\MerchantClientFactory;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Cart;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Exceptions\InvalidConnectionDetailsException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\HostedTokenization;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Token;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\ContextLogProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\HostedTokenizationProxyInterface;
use Throwable;
/**
 * Class HostedTokenizationProxy.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies
 */
class HostedTokenizationProxy implements HostedTokenizationProxyInterface
{
    private MerchantClientFactory $clientFactory;
    public function __construct(MerchantClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }
    public function create(Cart $cart, array $savedTokens = [], ?PaymentProductId $productId = null, string $template = ''): HostedTokenization
    {
        ContextLogProvider::getInstance()->setCurrentOrder($cart->getMerchantReference());
        return CreateHostedTokenizationResponseTransformer::transform($this->clientFactory->get()->hostedTokenization()->createHostedTokenization(CreateHostedTokenizationRequestTransformer::transform($cart, $savedTokens, $productId, $template)));
    }
    public function getToken(string $customerId, string $tokenId): ?Token
    {
        try {
            return TokenResponseTransformer::transform($customerId, $this->clientFactory->get()->tokens()->getToken($tokenId));
        } catch (Throwable $e) {
            return null;
        }
    }
    /**
     * @param string $tokenId
     *
     * @return void
     *
     * @throws InvalidConnectionDetailsException
     */
    public function deleteToken(string $tokenId): void
    {
        $this->clientFactory->get()->tokens()->deleteToken($tokenId);
    }
}
