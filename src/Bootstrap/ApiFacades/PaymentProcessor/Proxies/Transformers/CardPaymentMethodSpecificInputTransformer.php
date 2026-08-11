<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Cart\Cart;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Token;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\CreditCard;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ExemptionType;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodCollection;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentProductId;
use OnlinePayments\Sdk\Domain\CardPaymentMethodSpecificInput;
use OnlinePayments\Sdk\Domain\PaymentProduct130SpecificInput;
use OnlinePayments\Sdk\Domain\PaymentProduct130SpecificThreeDSecure;
use OnlinePayments\Sdk\Domain\RedirectionData;
use OnlinePayments\Sdk\Domain\ThreeDSecure;
/**
 * Class CardPaymentMethodSpecificInputTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers
 */
class CardPaymentMethodSpecificInputTransformer
{
    public static function transform(Cart $cart, string $getReturnUrl, ThreeDSSettings $cardsSettings, PaymentSettings $paymentSettings, ?PaymentMethodCollection $paymentMethodCollection = null, ?PaymentProductId $paymentProductId = null, ?Token $token = null, ?PaymentAction $paymentAction = null): CardPaymentMethodSpecificInput
    {
        $paymentMethodConfig = $paymentProductId !== null ? $paymentMethodCollection->get($paymentProductId) : null;
        $cardPaymentMethodSpecificInput = new CardPaymentMethodSpecificInput();
        if (null !== $token) {
            $cardPaymentMethodSpecificInput->setToken($token->getTokenId());
        }
        $redirectionData = new RedirectionData();
        $redirectionData->setReturnUrl($getReturnUrl);
        $threeDSecure = new ThreeDSecure();
        $threeDSecure->setRedirectionData($redirectionData);
        $threeDSecure->setSkipAuthentication(!$cardsSettings->isEnable3ds());
        if (null !== $paymentProductId && PaymentProductId::maestro()->equals($paymentProductId)) {
            $threeDSecure->setSkipAuthentication(\false);
        }
        if ($cardsSettings->isEnforceStrongAuthentication()) {
            $threeDSecure->setChallengeIndicator('challenge-required');
        }
        $exemptionType = self::resolveExemption($cart, $cardsSettings);
        if (null !== $exemptionType) {
            $threeDSecure->setExemptionRequest($exemptionType->getType());
            $threeDSecure->setSkipAuthentication(\false);
            $threeDSecure->setSkipSoftDecline(\false);
            $threeDSecure->setChallengeIndicator($exemptionType->equals(ExemptionType::transactionRiskAnalysis()) ? 'no-challenge-requested-risk-analysis-performed' : 'no-challenge-requested');
        }
        if ($cardsSettings->isEnable3ds()) {
            $paymentProduct130SpecificInput = new PaymentProduct130SpecificInput();
            $paymentProduct130ThreeDSecure = new PaymentProduct130SpecificThreeDSecure();
            // The usecase is sent whenever 3DSecure is enabled, regardless of exemptions.
            $paymentProduct130ThreeDSecure->setUsecase('single-amount');
            $paymentProduct130ThreeDSecure->setNumberOfItems(min($cart->getLineItems()->getQuantitySum(), 99));
            $paymentProduct130ThreeDSecure->setAcquirerExemption(null !== $exemptionType && $exemptionType->equals(ExemptionType::transactionRiskAnalysis()));
            $paymentProduct130SpecificInput->setThreeDSecure($paymentProduct130ThreeDSecure);
            $cardPaymentMethodSpecificInput->setPaymentProduct130SpecificInput($paymentProduct130SpecificInput);
        }
        $cardPaymentMethodSpecificInput->setThreeDSecure($threeDSecure);
        if ($paymentProductId !== null && $paymentProductId->equals(PaymentProductId::illicado()->getId())) {
            return $cardPaymentMethodSpecificInput;
        }
        $cardPaymentMethodSpecificInput->setAuthorizationMode($paymentSettings->getPaymentAction()->getType());
        if ($paymentAction) {
            $cardPaymentMethodSpecificInput->setAuthorizationMode($paymentAction->getType());
        }
        if ($paymentMethodConfig && $paymentMethodConfig->getPaymentAction()) {
            $cardPaymentMethodSpecificInput->setAuthorizationMode($paymentMethodConfig->getPaymentAction()->getType());
        }
        if ($paymentMethodConfig && $paymentMethodConfig->getAdditionalData() instanceof CreditCard) {
            $effectiveAction = $paymentMethodConfig->getPaymentAction() ?? $paymentAction ?? $paymentSettings->getPaymentAction();
            if ($effectiveAction->getType() === PaymentAction::AUTHORIZE) {
                $cardPaymentMethodSpecificInput->setAuthorizationMode($paymentMethodConfig->getAdditionalData()->getAuthorizationMode()->getType());
            }
        }
        if ($paymentProductId !== null && $paymentProductId->equals(PaymentProductId::mealvouchers()->getId())) {
            $cardPaymentMethodSpecificInput->setAuthorizationMode(PaymentAction::authorizeCapture()->getType());
        }
        if ($paymentProductId !== null && $paymentProductId->equals(PaymentProductId::chequeVacancesConnect()->getId())) {
            $cardPaymentMethodSpecificInput->setAuthorizationMode(PaymentAction::authorizeCapture()->getType());
        }
        // `hasWorldlineProductId()` rather than a `cards` comparison: the card parents are card types
        // too, and setting one of those non-numeric ids as a Worldline product id would send garbage.
        if ($paymentProductId !== null && $paymentProductId->isCardType() && $paymentProductId->hasWorldlineProductId()) {
            $cardPaymentMethodSpecificInput->setPaymentProductId($paymentProductId->getId());
        }
        if ($paymentProductId !== null && PaymentProductId::intersolve()->equals($paymentProductId->getId())) {
            $cardPaymentMethodSpecificInput->setAuthorizationMode(PaymentAction::authorizeCapture()->getType());
            if ($paymentMethodCollection && $config = $paymentMethodCollection->get(PaymentProductId::intersolve())) {
                $cardPaymentMethodSpecificInput->setPaymentProductId($config->getAdditionalData()->getProductId()->getId());
            }
        }
        return $cardPaymentMethodSpecificInput;
    }
    /**
     * Resolves the 3DS exemption to request for this cart, or null when no exemption applies.
     *
     * The configured exemption limit is always expressed in EUR (it is built as such in
     * PaymentMethodRequest::toDomainModel and in the ThreeDSSettings default), so it is compared
     * against the cart total already converted to EUR by the integration. No conversion is done
     * here. Per the functional requirements, when the shop has no EUR amount available the
     * exemption is not requested at all rather than compared in a different currency: "If EUR is
     * not configured, the 3DS exemption will not be applied, and the transaction will not have the
     * exemption requested."
     *
     * @param Cart $cart
     * @param ThreeDSSettings $cardsSettings
     *
     * @return ExemptionType|null
     */
    private static function resolveExemption(Cart $cart, ThreeDSSettings $cardsSettings): ?ExemptionType
    {
        if (!$cardsSettings->isEnable3ds() || !$cardsSettings->isEnable3dsExemption() || $cardsSettings->isEnforceStrongAuthentication()) {
            return null;
        }
        $exemptionType = $cardsSettings->getExemptionType();
        $totalInEur = $cart->getTotalInEUR();
        if (null === $exemptionType || null === $totalInEur) {
            return null;
        }
        $limit = $cardsSettings->getExemptionLimit();
        // Requesting an exemption weakens authentication, so a currency mismatch fails closed
        // instead of comparing two different currencies as if they were the same.
        if (!$totalInEur->getCurrency()->equal($limit->getCurrency())) {
            return null;
        }
        return $totalInEur->getValue() < $limit->getValue() ? $exemptionType : null;
    }
}
