<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Cart;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Token;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
use OnlinePayments\Sdk\Domain\CreateHostedTokenizationRequest;
use OnlinePayments\Sdk\Domain\PaymentProductFilterHostedTokenization;
use OnlinePayments\Sdk\Domain\PaymentProductFiltersHostedTokenization;
/**
 * Class CreateHostedTokenizationRequestTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers
 */
class CreateHostedTokenizationRequestTransformer
{
    /**
     * @param string[] $allowedBrands The brands Embedded Cards offers, when the request is for that
     *     method rather than for one specific brand.
     */
    public static function transform(Cart $cart, array $savedTokens = [], ?PaymentProductId $productId = null, string $template = '', string $fallbackLocale = 'en_GB', array $allowedBrands = []): CreateHostedTokenizationRequest
    {
        $request = new CreateHostedTokenizationRequest();
        $request->setAskConsumerConsent(!$cart->getCustomer()->isGuest());
        $request->setLocale($cart->getCustomer()->getFormattedLocale($fallbackLocale));
        $products = self::restrictedProducts($productId, $allowedBrands);
        if (!empty($products)) {
            $productFilter = new PaymentProductFiltersHostedTokenization();
            $filterRestriction = new PaymentProductFilterHostedTokenization();
            $filterRestriction->setProducts($products);
            $productFilter->setRestrictTo($filterRestriction);
            $request->setPaymentProductFilters($productFilter);
        }
        if (!empty($template)) {
            $request->setVariant($template);
        }
        if (!empty($savedTokens)) {
            $request->setTokens(join(',', array_map(function (Token $token) {
                return $token->getTokenId();
            }, $savedTokens)));
        }
        return $request;
    }
    /**
     * Which Worldline products the embedded form may show.
     *
     * Three cases, and the middle one is why this is not a one-liner:
     *
     *   * a specific brand - restrict to that brand;
     *   * Embedded Cards - restrict to the brands the merchant allows on it (functional requirements
     *     p11). Casting the method's own id would send product 0, since `embedded_cards` is this
     *     plugin's presentation of cards rather than a Worldline product;
     *   * anything else - no restriction, so the form shows whatever the account offers.
     *
     * @param string[] $allowedBrands
     *
     * @return int[]
     */
    private static function restrictedProducts(?PaymentProductId $productId, array $allowedBrands): array
    {
        if (null === $productId || !$productId->isCardType()) {
            return [];
        }
        if ($productId->hasWorldlineProductId()) {
            return [(int) $productId->getId()];
        }
        return array_map('intval', $allowedBrands);
    }
}
