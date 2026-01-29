<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Token;
use OnlinePayments\Sdk\Domain\TokenResponse;
/**
 * Class TokenResponseTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers
 */
class TokenResponseTransformer
{
    public static function transform(string $customerId, TokenResponse $tokenResponse): ?Token
    {
        if ($tokenResponse->getId() && \false === $tokenResponse->getIsTemporary() && $tokenResponse->getCard() && $tokenResponse->getCard()->getData() && $tokenResponse->getCard()->getData()->getCardWithoutCvv()) {
            return new Token($customerId, (string) $tokenResponse->getId(), (string) $tokenResponse->getPaymentProductId(), (string) $tokenResponse->getCard()->getData()->getCardWithoutCvv()->getCardNumber(), (string) $tokenResponse->getCard()->getData()->getCardWithoutCvv()->getExpiryDate());
        }
        return null;
    }
}
