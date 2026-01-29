<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers\CreatePaymentRequestTransformer;
use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers\CreatePaymentResponseTransformer;
use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers\PaymentCaptureResponseTransformer;
use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers\PaymentDetailsResponseTransformer;
use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers\PaymentRefundResponseTransformer;
use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers\PaymentResponseTransformer;
use WOP\OnlinePayments\Core\Bootstrap\Sdk\MerchantClientFactory;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentAction;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\GeneralSettings\PaymentSettings;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\PaymentRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\PaymentResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\HostedTokenization\Token;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\ContextLogProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Payment;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentCapture;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentDetails;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentRefund;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\MethodAdditionalData\ThreeDSSettings\ThreeDSSettings;
use WOP\OnlinePayments\Core\BusinessLogic\PaymentProcessor\Proxies\PaymentsProxyInterface;
/**
 * Class PaymentsProxy.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies
 */
class PaymentsProxy implements PaymentsProxyInterface
{
    private MerchantClientFactory $clientFactory;
    public function __construct(MerchantClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }
    public function create(PaymentRequest $request, ThreeDSSettings $cardsSettings, PaymentSettings $paymentSettings, ?Token $token = null, ?PaymentAction $paymentAction = null): PaymentResponse
    {
        ContextLogProvider::getInstance()->setCurrentOrder($request->getCartProvider()->get()->getMerchantReference());
        return CreatePaymentResponseTransformer::transform($this->clientFactory->get()->payments()->createPayment(CreatePaymentRequestTransformer::transform($request, $cardsSettings, $paymentSettings, $token, $paymentAction)));
    }
    public function getPaymentDetails(PaymentId $paymentId): PaymentDetails
    {
        ContextLogProvider::getInstance()->setPaymentNumber($paymentId->getTransactionId());
        return PaymentDetailsResponseTransformer::transform($this->clientFactory->get()->payments()->getPaymentDetails((string) $paymentId));
    }
    public function tryToGetPayment(PaymentId $paymentId): ?Payment
    {
        try {
            return $this->getPayment($paymentId);
        } catch (\Exception $e) {
            return null;
        }
    }
    public function getPayment(PaymentId $paymentId): Payment
    {
        ContextLogProvider::getInstance()->setPaymentNumber($paymentId->getTransactionId());
        return PaymentResponseTransformer::transform($this->clientFactory->get()->payments()->getPayment((string) $paymentId));
    }
    /**
     * @param PaymentId $paymentId
     *
     * @return PaymentRefund[]
     */
    public function getRefunds(PaymentId $paymentId): array
    {
        ContextLogProvider::getInstance()->setPaymentNumber($paymentId->getTransactionId());
        $refunds = $this->clientFactory->get()->refunds()->getRefunds((string) $paymentId)->getRefunds();
        if (empty($refunds)) {
            return [];
        }
        return array_map(fn($refund) => PaymentRefundResponseTransformer::transform($refund), $refunds);
    }
    /**
     * @param PaymentId $paymentId
     *
     * @return PaymentCapture[]
     */
    public function getCaptures(PaymentId $paymentId): array
    {
        ContextLogProvider::getInstance()->setPaymentNumber($paymentId->getTransactionId());
        $captures = $this->clientFactory->get()->captures()->getCaptures((string) $paymentId)->getCaptures();
        if (empty($captures)) {
            return [];
        }
        return array_map(fn($capture) => PaymentCaptureResponseTransformer::transform($capture), $captures);
    }
}
