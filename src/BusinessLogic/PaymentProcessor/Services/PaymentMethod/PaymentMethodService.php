<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\PaymentMethod;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\CartProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\MemoryCachingCartProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\SurchargeRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\SurchargeResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodCollection;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\CreditCard;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\PaymentMethodProxyInterface;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\SurchargeProxyInterface;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Repositories\PaymentMethodConfigRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Repositories\ProductTypeRepositoryInterface;
/**
 * Class PaymentMethodService.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\PaymentMethod
 */
class PaymentMethodService
{
    private PaymentMethodConfigRepositoryInterface $paymentMethodConfigRepository;
    private ProductTypeRepositoryInterface $productTypeRepository;
    private PaymentMethodProxyInterface $paymentMethodProxy;
    private SurchargeProxyInterface $surchargeProxy;
    public function __construct(PaymentMethodConfigRepositoryInterface $paymentMethodConfigRepository, ProductTypeRepositoryInterface $productTypeRepository, PaymentMethodProxyInterface $paymentMethodProxy, SurchargeProxyInterface $surchargeProxy)
    {
        $this->paymentMethodConfigRepository = $paymentMethodConfigRepository;
        $this->productTypeRepository = $productTypeRepository;
        $this->paymentMethodProxy = $paymentMethodProxy;
        $this->surchargeProxy = $surchargeProxy;
    }
    /**
     * Both of these serve the EMBEDDED (iframe) flow - they are consumed by HostedTokenizationService -
     * so they read Embedded Cards, which is what that flow renders.
     */
    public function getCardsPaymentAction(): ?PaymentAction
    {
        $cardConfig = $this->paymentMethodConfigRepository->getPaymentMethod(PaymentProductId::EMBEDDED_CARDS);
        return $cardConfig ? $cardConfig->getPaymentAction() : null;
    }
    public function getCardsTemplate(): string
    {
        $cardConfig = $this->paymentMethodConfigRepository->getPaymentMethod(PaymentProductId::EMBEDDED_CARDS);
        return $cardConfig ? $cardConfig->getTemplate() : '';
    }
    /**
     * The locale the EMBEDDED flow falls back to: Embedded Cards' own if it set one, else the store's.
     */
    public function getEmbeddedCardsFallbackLocale(string $storeFallbackLocale): string
    {
        $cardConfig = $this->paymentMethodConfigRepository->getPaymentMethod(PaymentProductId::EMBEDDED_CARDS);
        return $cardConfig ? $cardConfig->resolveFallbackLocale($storeFallbackLocale) : $storeFallbackLocale;
    }
    /**
     * The brands the embedded card form may offer (functional requirements p11). An unconfigured
     * store gets every brand, which is the documented default.
     *
     * @return string[]
     */
    public function getEmbeddedCardsAllowedBrands(): array
    {
        $cardConfig = $this->paymentMethodConfigRepository->getPaymentMethod(PaymentProductId::EMBEDDED_CARDS);
        $additionalData = $cardConfig ? $cardConfig->getAdditionalData() : null;
        return $additionalData instanceof CreditCard ? $additionalData->getAllowedBrands() : PaymentProductId::CARD_BRANDS;
    }
    /**
     * @param CartProvider $cartProvider
     * @return PaymentMethodCollection
     */
    public function getAvailablePaymentMethods(CartProvider $cartProvider): PaymentMethodCollection
    {
        $cartProvider = new MemoryCachingCartProvider($cartProvider);
        $enabledPaymentMethods = $this->paymentMethodConfigRepository->getEnabled();
        $availablePaymentMethods = $this->paymentMethodProxy->getAvailablePaymentMethods($cartProvider->get());
        // A card brand is a payment method of its own (ADR-0002), so it needs no special handling
        // here: the intersect offers it on its own enabled flag, carrying its own configuration, the
        // same way it does for every other product.
        $result = $enabledPaymentMethods->intersect($availablePaymentMethods);
        // The card parents and `hosted_checkout` have no Worldline product id, so the products API can
        // never report them and they cannot come out of the intersect. They are offered on
        // configuration alone.
        foreach ([PaymentProductId::embeddedCards(), PaymentProductId::redirectionToCards()] as $cardParent) {
            if ($parentMethod = $enabledPaymentMethods->get($cardParent)) {
                $result->add($parentMethod);
            }
        }
        if ($hostedCheckout = $enabledPaymentMethods->get(PaymentProductId::hostedCheckout())) {
            $result->add($hostedCheckout);
        }
        // Note what is NOT here any more: brands used to be removed from the offered set whenever the
        // single `cards` method was grouped. Functional requirements p11 makes the three card types
        // independent - "all three can be enabled and available on the checkout" - so a grouped parent
        // and a standalone Visa button now coexist, each on its own enabled flag. A brand only appears
        // if the merchant enabled it, which is a V2-only action, so no upgraded store gains buttons.
        if ($result->has(PaymentProductId::mealvouchers()) && !$this->isMealvouchersEligible($cartProvider)) {
            $result->remove([PaymentProductId::mealvouchers()]);
        }
        return $result->sortedByDisplayOrder();
    }
    /**
     * @param SurchargeRequest $surcharge
     * @return SurchargeResponse|null
     */
    public function calculateSurcharge(SurchargeRequest $surcharge): ?SurchargeResponse
    {
        return $this->surchargeProxy->calculateSurcharge($surcharge);
    }
    private function isMealvouchersEligible(CartProvider $cartProvider): bool
    {
        if ($cartProvider->get()->getCustomer()->isGuest() || empty($cartProvider->get()->getCustomer()->getMerchantCustomerId()) || empty($cartProvider->get()->getCustomer()->getContactDetails()->getEmail())) {
            return \false;
        }
        $productTypeMap = $this->productTypeRepository->getProductTypesMap($cartProvider);
        return !empty($productTypeMap);
    }
}
