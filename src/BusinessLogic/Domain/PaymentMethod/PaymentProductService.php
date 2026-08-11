<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod;

use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\PaymentMethodProxyInterface;
/**
 * Class PaymentProductService
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod
 */
class PaymentProductService
{
    /**
     * Default country/currency used to fetch payment product availability for the admin config flow,
     * which (unlike checkout) has no cart to derive them from. Worldline requires both, and the
     * combination is known to under-report some products (e.g. Twint, Przelewy24), hence the forced
     * inclusion below regardless of what this query returns.
     */
    private const DEFAULT_COUNTRY_CODE = 'FR';
    private const DEFAULT_CURRENCY_CODE = 'EUR';
    /**
     * Always considered available, on top of whatever the products API reports for the default
     * country/currency query.
     *
     * Twint and Przelewy24 because the FR/EUR query under-reports them; Pay by Link because it is the
     * plugin's own delivery mechanism rather than a Worldline product, so the products API can never
     * report it, and the V2 functional requirements (p11) require that it "is always present".
     *
     * Embedded Cards and Redirection to Cards for the same reason as Pay by Link: they are this
     * plugin's two ways of presenting cards, not Worldline products, so the products API only ever
     * returns the individual brands behind them. Omitted here they would both be reported unavailable
     * on every store.
     */
    private const FORCED_AVAILABLE_PRODUCTS = [PaymentProductId::TWINT, PaymentProductId::PRZELEWY24, PaymentProductId::PAY_BY_LINK, PaymentProductId::EMBEDDED_CARDS, PaymentProductId::REDIRECTION_TO_CARDS];
    private PaymentMethodProxyInterface $paymentMethodProxy;
    public function __construct(PaymentMethodProxyInterface $paymentMethodProxy)
    {
        $this->paymentMethodProxy = $paymentMethodProxy;
    }
    /**
     * The whole statically supported catalogue, card brands included.
     *
     * Card brands used to be excluded on request (`getSupportedPaymentMethods(false)`, used only by
     * the admin listing) because they were not configurable. ADR-0002 made every brand a payment
     * method of its own, so nothing needs the reduced list any more and the flag is gone with it.
     *
     * @return string[]
     */
    public function getSupportedPaymentMethods(): array
    {
        return PaymentProductId::SUPPORTED_PAYMENT_PRODUCTS;
    }
    /**
     * Product ids Worldline currently reports as available for this merchant account (intersected with
     * the statically supported list by GetPaymentProductsResponseTransformer), plus the always-forced
     * ids on top.
     *
     * @return string[]
     */
    public function getAvailableProductIds(): array
    {
        $available = $this->paymentMethodProxy->getSupportedPaymentMethods(self::DEFAULT_COUNTRY_CODE, self::DEFAULT_CURRENCY_CODE);
        $productIds = array_map(static function (PaymentMethod $paymentMethod): string {
            return $paymentMethod->getProductId()->getId();
        }, $available->toArray());
        foreach (self::FORCED_AVAILABLE_PRODUCTS as $forcedProductId) {
            if (!in_array($forcedProductId, $productIds, \true)) {
                $productIds[] = $forcedProductId;
            }
        }
        return $productIds;
    }
}
