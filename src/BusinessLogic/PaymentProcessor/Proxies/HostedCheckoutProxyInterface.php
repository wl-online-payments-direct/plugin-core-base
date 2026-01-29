<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedCheckout\HostedCheckoutSessionRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\PaymentResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Token;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodCollection;
/**
 * Interface HostedCheckoutProxyInterface.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies
 */
interface HostedCheckoutProxyInterface
{
    public function createSession(HostedCheckoutSessionRequest $request, ThreeDSSettings $cardsSettings, PaymentSettings $paymentSettings, PaymentMethodCollection $paymentMethodCollection, array $supportedPaymentMethods, ?Token $token = null): PaymentResponse;
}
