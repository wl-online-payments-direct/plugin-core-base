<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\PaymentRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\PaymentResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Token;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Payment;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentDetails;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
/**
 * Interface PaymentsProxyInterface.
 *
 * @package OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies
 */
interface PaymentsProxyInterface
{
    public function create(PaymentRequest $request, ThreeDSSettings $cardsSettings, PaymentSettings $paymentSettings, ?Token $token = null, ?PaymentAction $paymentAction = null): PaymentResponse;
    public function getPaymentDetails(PaymentId $paymentId): PaymentDetails;
    public function tryToGetPayment(PaymentId $paymentId): ?Payment;
    public function getPayment(PaymentId $paymentId): Payment;
    public function getRefunds(PaymentId $paymentId): array;
    public function getCaptures(PaymentId $paymentId): array;
}
