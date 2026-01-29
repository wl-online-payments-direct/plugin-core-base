<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Services\PaymentMethod;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\CartProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\MemoryCachingCartProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\SurchargeRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\SurchargeResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodCollection;
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
    public function getCardsPaymentAction(): ?PaymentAction
    {
        $cardConfig = $this->paymentMethodConfigRepository->getPaymentMethod((string) PaymentProductId::cards());
        return $cardConfig ? $cardConfig->getPaymentAction() : null;
    }
    public function getCardsTemplate(): string
    {
        $cardConfig = $this->paymentMethodConfigRepository->getPaymentMethod((string) PaymentProductId::cards());
        return $cardConfig ? $cardConfig->getTemplate() : '';
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
        $result = $enabledPaymentMethods->intersect($availablePaymentMethods);
        $result->remove(PaymentProductId::getAllCardBrands());
        $cardsPaymentMethod = $enabledPaymentMethods->get(PaymentProductId::cards());
        if ($cardsPaymentMethod) {
            $result->add($cardsPaymentMethod);
        }
        if ($cardsPaymentMethod && !$result->isCardsGroupingEnabled()) {
            foreach (PaymentProductId::getAllCardBrands() as $cardBrandProductId) {
                if ($cardBrandPaymentMethod = $availablePaymentMethods->get($cardBrandProductId)) {
                    $result->add($cardBrandPaymentMethod);
                }
            }
        }
        if ($hostedCheckout = $enabledPaymentMethods->get(PaymentProductId::hostedCheckout())) {
            $result->add($hostedCheckout);
        }
        if ($result->has(PaymentProductId::mealvouchers()) && !$this->isMealvouchersEligible($cartProvider)) {
            $result->remove([PaymentProductId::mealvouchers()]);
        }
        return $result;
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
