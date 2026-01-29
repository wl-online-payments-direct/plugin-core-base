<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers;

use DateInterval;
use DateTime;
use DateTimeZone;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PayByLinkExpirationTime;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PayByLinkSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentLinks\PaymentLinkRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodCollection;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
use OnlinePayments\Sdk\Domain\CardPaymentMethodSpecificInputForHostedCheckout;
use OnlinePayments\Sdk\Domain\CreatePaymentLinkRequest;
use OnlinePayments\Sdk\Domain\HostedCheckoutSpecificInput;
use OnlinePayments\Sdk\Domain\MobilePaymentMethodSpecificInput;
use OnlinePayments\Sdk\Domain\PaymentLinkSpecificInput;
use OnlinePayments\Sdk\Domain\PaymentProductFilter;
use OnlinePayments\Sdk\Domain\PaymentProductFiltersHostedCheckout;
use OnlinePayments\Sdk\Domain\RedirectPaymentMethodSpecificInput;
use OnlinePayments\Sdk\Domain\RedirectPaymentProduct5402SpecificInput;
/**
 * Class CreatePaymentLinkRequestTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers
 */
class CreatePaymentLinkRequestTransformer
{
    public static function transform(PaymentLinkRequest $input, ThreeDSSettings $cardsSettings, PaymentSettings $paymentSettings, PayByLinkSettings $payByLinkSettings, PaymentMethodCollection $paymentMethodCollection, array $supportedPaymentMethods): CreatePaymentLinkRequest
    {
        $cart = $input->getCartProvider()->get();
        $request = new CreatePaymentLinkRequest();
        $specificInput = new PaymentLinkSpecificInput();
        $specificInput->setExpirationDate(self::getExpirationDate($input->getExpiresAt(), $payByLinkSettings->getExpirationTime()));
        $request->setPaymentLinkSpecificInput($specificInput);
        $hostedCheckoutSpecificInput = new HostedCheckoutSpecificInput();
        $hostedCheckoutSpecificInput->setReturnUrl($input->getReturnUrl());
        $hostedCheckoutSpecificInput->setAllowedNumberOfPaymentAttempts($paymentSettings->getPaymentAttemptsNumber()->getPaymentAttemptsNumber());
        $filters = new PaymentProductFiltersHostedCheckout();
        $productFilter = new PaymentProductFilter();
        $productFilter->setProducts(array_map('intval', PaymentProductId::getForHostedCheckoutPage($supportedPaymentMethods)));
        $filters->setRestrictTo($productFilter);
        $hostedCheckoutSpecificInput->setPaymentProductFilters($filters);
        if ($config = $paymentMethodCollection->get(PaymentProductId::hostedCheckout())) {
            $cardSpecificInputForHostedCheckout = new CardPaymentMethodSpecificInputForHostedCheckout();
            $cardSpecificInputForHostedCheckout->setGroupCards($config->getAdditionalData()->isEnableGroupCards());
            $hostedCheckoutSpecificInput->setCardPaymentMethodSpecificInput($cardSpecificInputForHostedCheckout);
        }
        $request->setOrder(OrderTransformer::transform($cart));
        $request->setHostedCheckoutSpecificInput($hostedCheckoutSpecificInput);
        $cardPaymentMethodSpecificInput = CardPaymentMethodSpecificInputTransformer::transform($cart, $input->getReturnUrl(), $cardsSettings, $paymentSettings, $paymentMethodCollection);
        $request->setCardPaymentMethodSpecificInput($cardPaymentMethodSpecificInput);
        $mobilePaymentMethodSpecificInput = new MobilePaymentMethodSpecificInput();
        $mobilePaymentMethodSpecificInput->setPaymentProduct320SpecificInput(GooglePaySpecificRequestTransformer::transform($cardPaymentMethodSpecificInput));
        $mobilePaymentMethodSpecificInput->setAuthorizationMode($paymentSettings->getPaymentAction()->getType());
        $request->setMobilePaymentMethodSpecificInput($mobilePaymentMethodSpecificInput);
        $redirectPaymentMethodSpecificInput = new RedirectPaymentMethodSpecificInput();
        $redirectPaymentProduct5402SpecificInput = new RedirectPaymentProduct5402SpecificInput();
        $redirectPaymentProduct5402SpecificInput->setCompleteRemainingPaymentAmount(\true);
        $redirectPaymentMethodSpecificInput->setRequiresApproval(PaymentAction::authorize()->equals($paymentSettings->getPaymentAction()));
        $redirectPaymentMethodSpecificInput->setPaymentProduct5402SpecificInput($redirectPaymentProduct5402SpecificInput);
        $request->setRedirectPaymentMethodSpecificInput($redirectPaymentMethodSpecificInput);
        return $request;
    }
    private static function getExpirationDate(?DateTime $requestExpiresAt, PayByLinkExpirationTime $expirationTime): DateTime
    {
        if ($requestExpiresAt) {
            $expiresAt = $requestExpiresAt;
        } else {
            $expiresAt = new DateTime('now', new DateTimeZone('UTC'));
            $expiresAt->add(new DateInterval('P' . $expirationTime->getDays() . 'D'));
        }
        $expiresAt->setTime(23, 59, 59);
        return $expiresAt;
    }
}
