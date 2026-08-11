<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedCheckout\HostedCheckoutSessionRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Token;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\CreditCard;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\Descriptor;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodCollection;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
use OnlinePayments\Sdk\Domain\CardPaymentMethodSpecificInputForHostedCheckout;
use OnlinePayments\Sdk\Domain\CreateHostedCheckoutRequest;
use OnlinePayments\Sdk\Domain\CreateMandateRequest;
use OnlinePayments\Sdk\Domain\HostedCheckoutSpecificInput;
use OnlinePayments\Sdk\Domain\MobilePaymentMethodSpecificInput;
use OnlinePayments\Sdk\Domain\Order;
use OnlinePayments\Sdk\Domain\PaymentProductFilter;
use OnlinePayments\Sdk\Domain\PaymentProductFiltersHostedCheckout;
use OnlinePayments\Sdk\Domain\RedirectPaymentMethodSpecificInput;
use OnlinePayments\Sdk\Domain\RedirectPaymentProduct5402SpecificInput;
use OnlinePayments\Sdk\Domain\RedirectPaymentProduct5403SpecificInput;
use OnlinePayments\Sdk\Domain\RedirectPaymentProduct5408SpecificInput;
use OnlinePayments\Sdk\Domain\SepaDirectDebitPaymentMethodSpecificInputBase;
use OnlinePayments\Sdk\Domain\SepaDirectDebitPaymentProduct771SpecificInputBase;
use OnlinePayments\Sdk\Domain\SurchargeSpecificInput;
/**
 * Class CreateHostedCheckoutRequestTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers
 */
class CreateHostedCheckoutRequestTransformer
{
    public static function transform(HostedCheckoutSessionRequest $input, ThreeDSSettings $cardsSettings, PaymentSettings $paymentSettings, PaymentMethodCollection $paymentMethodCollection, array $supportedPaymentMethods, ?Token $token = null): CreateHostedCheckoutRequest
    {
        $cart = $input->getCartProvider()->get();
        $request = new CreateHostedCheckoutRequest();
        $paymentProductId = $input->getPaymentProductId() ?: PaymentProductId::hostedCheckout();
        // Functional requirements: the fallback locale "should also be available per PM". The method's
        // own value wins; empty means it never set one and the store value applies.
        $fallbackLocale = self::resolveFallbackLocale($paymentMethodCollection, $paymentProductId, $paymentSettings->getFallbackLocale());
        $hostedCheckoutSpecificInput = new HostedCheckoutSpecificInput();
        $hostedCheckoutSpecificInput->setReturnUrl($input->getReturnUrl());
        $hostedCheckoutSpecificInput->setLocale($cart->getCustomer()->getFormattedLocale($fallbackLocale));
        $hostedCheckoutSpecificInput->setAllowedNumberOfPaymentAttempts($paymentSettings->getPaymentAttemptsNumber()->getPaymentAttemptsNumber());
        $hostedCheckoutSpecificInput->setShowResultPage(!$paymentSettings->isSkipConfirmationPage());
        $hostedCheckoutSpecificInput->setSessionTimeout($paymentSettings->getSessionTimeout()->getMinutes());
        if ($config = $paymentMethodCollection->get($paymentProductId)) {
            $hostedCheckoutSpecificInput->setVariant($config->getTemplate());
        }
        $filters = new PaymentProductFiltersHostedCheckout();
        $productFilter = new PaymentProductFilter();
        $productFilter->setProducts(array_map('intval', PaymentProductId::getForHostedCheckoutPage($supportedPaymentMethods)));
        // A grouped card request restricts to the `cards` GROUP - a Worldline group name, unrelated to
        // our product ids - rather than to a product. Anything else restricts to its own product id,
        // which is why the guard must cover every id Worldline does not know as a product: casting one
        // of those would send product 0.
        if (null !== $input->getPaymentProductId() && !$paymentProductId->isGroupedCardsRequest()) {
            $productFilter->setProducts([(int) $input->getPaymentProductId()->getId()]);
        }
        if (null !== $input->getPaymentProductId() && $paymentProductId->isGroupedCardsRequest()) {
            $allowedBrands = self::allowedBrandsFor($paymentMethodCollection, $paymentProductId);
            if (count($allowedBrands) === count(PaymentProductId::CARD_BRANDS)) {
                // Everything is allowed, which is the default. Restrict by GROUP, exactly as before -
                // the group is the wider statement, covering card products Worldline offers that this
                // plugin has no constant for.
                $productFilter->setProducts([]);
                $productFilter->setGroups(['cards']);
            } else {
                // The merchant narrowed the brand list, and a group filter cannot express that, so the
                // restriction has to be by product. Functional requirements p11 gives both parents an
                // allowed-brands list; leaving the group here would offer the brands they removed.
                $productFilter->setProducts(array_map('intval', $allowedBrands));
            }
        }
        $filters->setRestrictTo($productFilter);
        $hostedCheckoutSpecificInput->setPaymentProductFilters($filters);
        if ($config && $config->getAdditionalData() instanceof Descriptor && $config->getAdditionalData()->getDescriptor() !== '' && ($cart->getDescriptor() === null || $cart->getDescriptor() === '')) {
            $cart->setDescriptor($config->getAdditionalData()->getDescriptor());
        }
        $order = OrderTransformer::transform($cart, $paymentSettings->isSendShoppingCart(), $fallbackLocale);
        if ($paymentSettings->isApplySurcharge()) {
            $surchargeSpecificInput = new SurchargeSpecificInput();
            $surchargeSpecificInput->setMode('on-behalf-of');
            $order->setSurchargeSpecificInput($surchargeSpecificInput);
        }
        $request->setOrder($order);
        $request->setHostedCheckoutSpecificInput($hostedCheckoutSpecificInput);
        $cardPaymentMethodSpecificInput = CardPaymentMethodSpecificInputTransformer::transform($cart, $input->getReturnUrl(), $cardsSettings, $paymentSettings, $paymentMethodCollection, $input->getPaymentProductId(), $token);
        $request->setCardPaymentMethodSpecificInput($cardPaymentMethodSpecificInput);
        $mobilePaymentMethodSpecificInput = new MobilePaymentMethodSpecificInput();
        $mobilePaymentMethodSpecificInput->setAuthorizationMode(PaymentAction::authorizeCapture()->getType());
        if ($paymentProductId !== null && $paymentProductId->isSeparateCaptureSupported()) {
            $mobilePaymentMethodSpecificInput->setAuthorizationMode($config->getPaymentAction() ? $config->getPaymentAction()->getType() : $paymentSettings->getPaymentAction()->getType());
        }
        if (null !== $input->getPaymentProductId() && $input->getPaymentProductId()->isMobileType()) {
            $mobilePaymentMethodSpecificInput->setPaymentProductId($input->getPaymentProductId()->getId());
        }
        $mobilePaymentMethodSpecificInput->setPaymentProduct320SpecificInput(GooglePaySpecificRequestTransformer::transform($cardPaymentMethodSpecificInput));
        $request->setMobilePaymentMethodSpecificInput($mobilePaymentMethodSpecificInput);
        $redirectPaymentMethodSpecificInput = new RedirectPaymentMethodSpecificInput();
        $redirectPaymentMethodSpecificInput->setRequiresApproval(\false);
        if ($paymentProductId !== null && $paymentProductId->isSeparateCaptureSupported()) {
            $redirectPaymentMethodSpecificInput->setRequiresApproval(PaymentAction::authorize()->equals($config->getPaymentAction() ?: $paymentSettings->getPaymentAction()));
        }
        if ($input->getPaymentProductId() !== null && $input->getPaymentProductId()->equals(PaymentProductId::mealvouchers())) {
            $redirectPaymentProduct5402SpecificInput = new RedirectPaymentProduct5402SpecificInput();
            $redirectPaymentProduct5402SpecificInput->setCompleteRemainingPaymentAmount(\true);
            $redirectPaymentMethodSpecificInput->setPaymentProduct5402SpecificInput($redirectPaymentProduct5402SpecificInput);
            $redirectPaymentMethodSpecificInput->setRequiresApproval(\false);
            // Reset mobile specific input because it breaks mealvouchers
            $request->setMobilePaymentMethodSpecificInput(null);
        }
        if ($input->getPaymentProductId() !== null && $input->getPaymentProductId()->equals(PaymentProductId::chequeVacancesConnect())) {
            $redirectPaymentProduct5403SpecificInput = new RedirectPaymentProduct5403SpecificInput();
            $redirectPaymentProduct5403SpecificInput->setCompleteRemainingPaymentAmount(\true);
            $redirectPaymentMethodSpecificInput->setPaymentProduct5403SpecificInput($redirectPaymentProduct5403SpecificInput);
            $redirectPaymentMethodSpecificInput->setRequiresApproval(\false);
            $request->setMobilePaymentMethodSpecificInput(null);
        }
        if ($input->getPaymentProductId() !== null && $input->getPaymentProductId()->equals(PaymentProductId::intersolve())) {
            $redirectPaymentMethodSpecificInput->setRequiresApproval(\false);
            // Reset mobile specific input because it breaks intersolve
            $request->setMobilePaymentMethodSpecificInput(null);
        }
        if (null !== $input->getPaymentProductId() && $input->getPaymentProductId()->equals(PaymentProductId::illicado()->getId())) {
            $redirectPaymentMethodSpecificInput->setRequiresApproval(\false);
        }
        if ($input->getPaymentProductId() !== null && $input->getPaymentProductId()->isRedirectType() && !$input->getPaymentProductId()->equals(PaymentProductId::sepaDirectDebit()->getId())) {
            $redirectPaymentMethodSpecificInput->setPaymentProductId((int) $input->getPaymentProductId()->getId());
        }
        self::setHostedCheckoutSpecificInput($paymentMethodCollection, $paymentProductId, $hostedCheckoutSpecificInput);
        self::setIntersolveSpecificInput($paymentMethodCollection, $hostedCheckoutSpecificInput);
        self::setSepaSpecificInput($paymentMethodCollection, $order, $request, $cart->getCustomer()->getFormattedLocale());
        self::setBankTransferSpecificInput($paymentMethodCollection, $redirectPaymentMethodSpecificInput);
        self::setOneySpecificInput($input, $paymentMethodCollection, $redirectPaymentMethodSpecificInput);
        $request->setRedirectPaymentMethodSpecificInput($redirectPaymentMethodSpecificInput);
        return $request;
    }
    /**
     * @param PaymentMethodCollection $paymentMethodCollection
     * @param HostedCheckoutSpecificInput $hostedCheckoutSpecificInput
     *
     * @return void
     */
    /**
     * The locale to fall back to: the requested method's own if it set one, else the store's.
     *
     * @return string
     */
    private static function resolveFallbackLocale(PaymentMethodCollection $paymentMethodCollection, PaymentProductId $paymentProductId, string $storeFallbackLocale): string
    {
        $method = $paymentMethodCollection->get($paymentProductId);
        return $method ? $method->resolveFallbackLocale($storeFallbackLocale) : $storeFallbackLocale;
    }
    /**
     * The brands the requested card parent offers, defaulting to all of them when the method has no
     * saved configuration - which is what an unconfigured store presented before this field existed.
     *
     * @return string[]
     */
    private static function allowedBrandsFor(PaymentMethodCollection $paymentMethodCollection, PaymentProductId $paymentProductId): array
    {
        $method = $paymentMethodCollection->get($paymentProductId);
        $additionalData = $method ? $method->getAdditionalData() : null;
        return $additionalData instanceof CreditCard ? $additionalData->getAllowedBrands() : PaymentProductId::CARD_BRANDS;
    }
    protected static function setHostedCheckoutSpecificInput(PaymentMethodCollection $paymentMethodCollection, PaymentProductId $paymentProductId, HostedCheckoutSpecificInput $hostedCheckoutSpecificInput): void
    {
        if ($config = $paymentMethodCollection->get(PaymentProductId::hostedCheckout())) {
            $cardSpecificInputForHostedCheckout = new CardPaymentMethodSpecificInputForHostedCheckout();
            $cardSpecificInputForHostedCheckout->setGroupCards($config->getAdditionalData()->isEnableGroupCards());
            $hostedCheckoutSpecificInput->setCardPaymentMethodSpecificInput($cardSpecificInputForHostedCheckout);
        }
        if (!$paymentProductId->isGroupedCardsRequest()) {
            return;
        }
        // Grouping is no longer read from the configuration: both card parents are always grouped
        // (functional requirements p11, ADR-0003 decision 12), so a request for one is grouped by
        // definition and there is no stored flag left to consult.
        $cardSpecificInputForHostedCheckout = new CardPaymentMethodSpecificInputForHostedCheckout();
        $cardSpecificInputForHostedCheckout->setGroupCards(\true);
        $hostedCheckoutSpecificInput->setCardPaymentMethodSpecificInput($cardSpecificInputForHostedCheckout);
    }
    /**
     * @param PaymentMethodCollection $paymentMethodCollection
     * @param HostedCheckoutSpecificInput $hostedCheckoutSpecificInput
     *
     * @return void
     */
    protected static function setIntersolveSpecificInput(PaymentMethodCollection $paymentMethodCollection, HostedCheckoutSpecificInput $hostedCheckoutSpecificInput): void
    {
        if ($config = $paymentMethodCollection->get(PaymentProductId::intersolve())) {
            $hostedCheckoutSpecificInput->setSessionTimeout($config->getAdditionalData()->getSessionTimeout()->getDuration());
        }
    }
    /**
     * @param PaymentMethodCollection $paymentMethodCollection
     * @param Order $order
     * @param CreateHostedCheckoutRequest $request
     * @param string $locale Checkout locale, used to derive the mandate language.
     *
     * @return void
     */
    protected static function setSepaSpecificInput(PaymentMethodCollection $paymentMethodCollection, Order $order, CreateHostedCheckoutRequest $request, string $locale): void
    {
        if ($config = $paymentMethodCollection->get(PaymentProductId::sepaDirectDebit())) {
            $references = $order->getReferences();
            $merchantReference = $references !== null ? (string) $references->getMerchantReference() : '';
            $customerReference = (string) $order->getCustomer()->getMerchantCustomerId();
            if ($customerReference === '') {
                $customerReference = $merchantReference;
            }
            $mandate = new CreateMandateRequest();
            $mandate->setCustomerReference($customerReference);
            $mandate->setLanguage(self::sepaMandateLanguage($locale));
            $mandate->setRecurrenceType('UNIQUE');
            $mandate->setSignatureType($config->getAdditionalData()->getSignatureType()->getType());
            $mandate->setUniqueMandateReference($merchantReference);
            $specificInput = new SepaDirectDebitPaymentProduct771SpecificInputBase();
            $specificInput->setMandate($mandate);
            $sepaDirectDebit = new SepaDirectDebitPaymentMethodSpecificInputBase();
            $sepaDirectDebit->setPaymentProductId((int) PaymentProductId::sepaDirectDebit()->getId());
            $sepaDirectDebit->paymentProduct771SpecificInput = $specificInput;
            $request->setSepaDirectDebitPaymentMethodSpecificInput($sepaDirectDebit);
        }
    }
    /**
     * SEPA mandate language: the two-letter language from the checkout locale, restricted to the set
     * Worldline accepts for the mandate (de, en, es, fr, it, nl, si, sk, sv); defaults to 'en'.
     *
     * @param string $locale
     *
     * @return string
     */
    private static function sepaMandateLanguage(string $locale): string
    {
        $allowed = ['de', 'en', 'es', 'fr', 'it', 'nl', 'si', 'sk', 'sv'];
        $language = strtolower(substr($locale, 0, 2));
        return in_array($language, $allowed, \true) ? $language : 'en';
    }
    /**
     * @param PaymentMethodCollection $paymentMethodCollection
     * @param RedirectPaymentMethodSpecificInput $redirectPaymentMethodSpecificInput
     *
     * @return void
     */
    protected static function setBankTransferSpecificInput(PaymentMethodCollection $paymentMethodCollection, RedirectPaymentMethodSpecificInput $redirectPaymentMethodSpecificInput): void
    {
        if ($config = $paymentMethodCollection->get(PaymentProductId::bankTransfer())) {
            $paymentProduct5408SpecificInput = new RedirectPaymentProduct5408SpecificInput();
            $paymentProduct5408SpecificInput->setInstantPaymentOnly($config->getAdditionalData()->isInstantPayment());
            $redirectPaymentMethodSpecificInput->setPaymentProduct5408SpecificInput($paymentProduct5408SpecificInput);
        }
    }
    /**
     * @param HostedCheckoutSessionRequest $input
     * @param PaymentMethodCollection $paymentMethodCollection
     * @param RedirectPaymentMethodSpecificInput $redirectPaymentMethodSpecificInput
     *
     * @return void
     */
    protected static function setOneySpecificInput(HostedCheckoutSessionRequest $input, PaymentMethodCollection $paymentMethodCollection, RedirectPaymentMethodSpecificInput $redirectPaymentMethodSpecificInput): void
    {
        if (!$input->getPaymentProductId()) {
            return;
        }
        if ($input->getPaymentProductId()->equals(PaymentProductId::ONEY_3X) && $config = $paymentMethodCollection->get(PaymentProductId::oney3x())) {
            $redirectPaymentMethodSpecificInput->setPaymentOption($config->getAdditionalData()->getPaymentOption());
        }
        if ($input->getPaymentProductId()->equals(PaymentProductId::ONEY_4X) && $config = $paymentMethodCollection->get(PaymentProductId::oney4x())) {
            $redirectPaymentMethodSpecificInput->setPaymentOption($config->getAdditionalData()->getPaymentOption());
        }
        if ($input->getPaymentProductId()->equals(PaymentProductId::ONEY_FINANCEMENT_LONG) && $config = $paymentMethodCollection->get(PaymentProductId::oneyFinancementLong())) {
            $redirectPaymentMethodSpecificInput->setPaymentOption($config->getAdditionalData()->getPaymentOption());
        }
        if ($input->getPaymentProductId()->equals(PaymentProductId::ONEY_BANK_CARD) && $config = $paymentMethodCollection->get(PaymentProductId::oneyBankCard())) {
            $redirectPaymentMethodSpecificInput->setPaymentOption($config->getAdditionalData()->getPaymentOption());
        }
    }
}
