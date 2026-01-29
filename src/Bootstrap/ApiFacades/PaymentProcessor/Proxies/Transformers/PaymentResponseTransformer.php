<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Exceptions\InvalidApiResponseException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Currency;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\Payment;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodDefaultConfigs;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
use OnlinePayments\Sdk\Domain\PaymentOutput;
use OnlinePayments\Sdk\Domain\PaymentResponse;
/**
 * Class PaymentResponseTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers
 */
class PaymentResponseTransformer
{
    public static function transform(PaymentResponse $payment): Payment
    {
        if (null === $payment->getPaymentOutput() || null === $payment->getStatusOutput() || null === $payment->getPaymentOutput()->getReferences() || null === $payment->getPaymentOutput()->getReferences()->getMerchantReference()) {
            throw new InvalidApiResponseException(new TranslatableLabel('Payment response is invalid. Payment status details missing in API response.', 'paymentProcessor.proxy.InvalidApiResponse'));
        }
        $productId = self::getProductId($payment->getPaymentOutput());
        $paymentMethodName = self::getPaymentMethodLabel($productId, $payment->getPaymentOutput());
        return new Payment(StatusCode::parse((int) $payment->getStatusOutput()->getStatusCode()), Amount::fromInt($payment->getPaymentOutput()->getAmountOfMoney()->getAmount(), Currency::fromIsoCode($payment->getPaymentOutput()->getAmountOfMoney()->getCurrencyCode())), self::getToken($payment->getPaymentOutput()), $payment->getStatus(), $productId, $paymentMethodName);
    }
    private static function getToken(PaymentOutput $paymentOutput): ?string
    {
        if ($paymentOutput->getCardPaymentMethodSpecificOutput() && !empty($paymentOutput->getCardPaymentMethodSpecificOutput()->getToken())) {
            return $paymentOutput->getCardPaymentMethodSpecificOutput()->getToken();
        }
        if ($paymentOutput->getRedirectPaymentMethodSpecificOutput() && !empty($paymentOutput->getRedirectPaymentMethodSpecificOutput()->getToken())) {
            return $paymentOutput->getRedirectPaymentMethodSpecificOutput()->getToken();
        }
        return null;
    }
    private static function getProductId(PaymentOutput $paymentOutput): ?string
    {
        switch ($paymentOutput->getPaymentMethod()) {
            case 'card':
            default:
                $output = $paymentOutput->getCardPaymentMethodSpecificOutput();
                break;
            case 'redirect':
                $output = $paymentOutput->getRedirectPaymentMethodSpecificOutput();
                break;
            case 'mobile':
                $output = $paymentOutput->getMobilePaymentMethodSpecificOutput();
                break;
            case 'sepa':
                $output = $paymentOutput->getSepaDirectDebitPaymentMethodSpecificOutput();
                break;
        }
        return $output ? (string) $output->getPaymentProductId() : null;
    }
    private static function getPaymentMethodLabel(?string $productId, PaymentOutput $paymentOutput): string
    {
        if (empty($productId)) {
            return '';
        }
        return array_key_exists($productId, PaymentMethodDefaultConfigs::PAYMENT_METHOD_CONFIGS) ? PaymentMethodDefaultConfigs::PAYMENT_METHOD_CONFIGS[$productId]['name']['translation'] : $paymentOutput->getPaymentMethod();
    }
}
