<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Exceptions\InvalidApiResponseException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Amount;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout\Currency;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentAmounts;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentDetails;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentOperation;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentSpecificOutput;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusError;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusOutput;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\PaymentMethod\PaymentMethodDefaultConfigs;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
use OnlinePayments\Sdk\Domain\CardPaymentMethodSpecificOutput;
use OnlinePayments\Sdk\Domain\PaymentDetailsResponse;
use OnlinePayments\Sdk\Domain\PaymentOutput;
use OnlinePayments\Sdk\Domain\PaymentStatusOutput;
/**
 * Class PaymentDetailsResponseTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\PaymentProcessor\Proxies\Transformers
 */
class PaymentDetailsResponseTransformer
{
    public static function transform(PaymentDetailsResponse $paymentDetails): PaymentDetails
    {
        if (null === $paymentDetails->getPaymentOutput() || null === $paymentDetails->getStatusOutput() || null === $paymentDetails->getPaymentOutput()->getReferences() || null === $paymentDetails->getPaymentOutput()->getReferences()->getMerchantReference()) {
            throw new InvalidApiResponseException(new TranslatableLabel('Payment response is invalid. Payment status details missing in API response.', 'paymentProcessor.proxy.InvalidApiResponse'));
        }
        $tokenId = null;
        if ($paymentDetails->getPaymentOutput()->getCardPaymentMethodSpecificOutput() && !empty($paymentDetails->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getToken())) {
            $tokenId = $paymentDetails->getPaymentOutput()->getCardPaymentMethodSpecificOutput()->getToken();
        }
        if (null === $tokenId && $paymentDetails->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput() && !empty($paymentDetails->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput()->getToken())) {
            $tokenId = $paymentDetails->getPaymentOutput()->getRedirectPaymentMethodSpecificOutput()->getToken();
        }
        $paymentOutput = $paymentDetails->getPaymentOutput();
        return new PaymentDetails(StatusCode::parse((int) $paymentDetails->getStatusOutput()->getStatusCode()), Amount::fromInt($paymentOutput->getAmountOfMoney()->getAmount(), Currency::fromIsoCode($paymentDetails->getPaymentOutput()->getAmountOfMoney()->getCurrencyCode())), $tokenId, self::getPaymentAmounts($paymentDetails), self::isFullyPaid($paymentDetails->getPaymentOutput()), self::getPaymentMethodLabel($paymentDetails->getPaymentOutput()), self::getStatusOutput($paymentDetails->getStatusOutput()), self::getPaymentSpecificOutput($paymentDetails->getPaymentOutput()), $paymentDetails->getStatus(), self::getOperations($paymentDetails));
    }
    /**
     * @param PaymentOutput $paymentOutput
     *
     * @return string
     */
    protected static function getPaymentMethodLabel(PaymentOutput $paymentOutput): string
    {
        $cardOutput = $paymentOutput->getCardPaymentMethodSpecificOutput();
        $redirectOutput = $paymentOutput->getRedirectPaymentMethodSpecificOutput();
        $mobileOutput = $paymentOutput->getMobilePaymentMethodSpecificOutput();
        $sepaOutput = $paymentOutput->getSepaDirectDebitPaymentMethodSpecificOutput();
        $id = $cardOutput && $cardOutput->getPaymentProductId() ? $cardOutput->getPaymentProductId() : ($redirectOutput ? $redirectOutput->getPaymentProductId() : ($mobileOutput ? $mobileOutput->getPaymentProductId() : ($sepaOutput ? $sepaOutput->getPaymentProductId() : '')));
        if (!$id) {
            return '';
        }
        return array_key_exists($id, PaymentMethodDefaultConfigs::PAYMENT_METHOD_CONFIGS) ? PaymentMethodDefaultConfigs::PAYMENT_METHOD_CONFIGS[$id]['name']['translation'] : $paymentOutput->getPaymentMethod();
    }
    /**
     * @param PaymentOutput $paymentOutput
     *
     * @return PaymentSpecificOutput
     */
    private static function getPaymentSpecificOutput(PaymentOutput $paymentOutput): PaymentSpecificOutput
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
        }
        $fraudResult = $output && $output->getFraudResults() ? $output->getFraudResults()->getFraudServiceResult() : null;
        $liability = null;
        $exemptionType = null;
        if ($output instanceof CardPaymentMethodSpecificOutput && $output->getThreeDSecureResults()) {
            $liability = $output->getThreeDSecureResults()->getLiability();
            $exemptionType = $output->getThreeDSecureResults()->getAppliedExemption();
        }
        return new PaymentSpecificOutput($output ? (string) $output->getPaymentProductId() : '', $fraudResult, $liability, $exemptionType, self::getSurchargeAmount($paymentOutput));
    }
    private static function getSurchargeAmount(PaymentOutput $paymentOutput): ?Amount
    {
        if (!$paymentOutput->getSurchargeSpecificOutput()) {
            return null;
        }
        return Amount::fromInt($paymentOutput->getSurchargeSpecificOutput()->getSurchargeAmount()->getAmount(), Currency::fromIsoCode($paymentOutput->getSurchargeSpecificOutput()->getSurchargeAmount()->getCurrencyCode()));
    }
    private static function getStatusOutput(PaymentStatusOutput $statusOutput): StatusOutput
    {
        $apiErrors = $statusOutput->getErrors() ?: [];
        $errors = [];
        foreach ($apiErrors as $apiError) {
            $errors[] = new StatusError($apiError->getId(), $apiError->getErrorCode());
        }
        return new StatusOutput($statusOutput->getIsAuthorized(), $statusOutput->getIsCancellable(), $statusOutput->getIsRefundable(), $errors);
    }
    private static function getOperations(PaymentDetailsResponse $paymentDetails): array
    {
        $operations = [];
        foreach ($paymentDetails->getOperations() as $operation) {
            $operations[] = new PaymentOperation(PaymentId::parse($operation->getId()), Amount::fromInt($operation->getAmountOfMoney()->getAmount(), Currency::fromIsoCode($operation->getAmountOfMoney()->getCurrencyCode())), StatusCode::parse((int) $operation->getStatusOutput()->getStatusCode()), $operation->getStatus());
        }
        return $operations;
    }
    private static function getPaymentAmounts(PaymentDetailsResponse $paymentDetails): PaymentAmounts
    {
        $refundedAmount = Amount::fromInt(0, Currency::fromIsoCode($paymentDetails->getPaymentOutput()->getAmountOfMoney()->getCurrencyCode()));
        $refundRequestedAmount = Amount::fromInt(0, Currency::fromIsoCode($paymentDetails->getPaymentOutput()->getAmountOfMoney()->getCurrencyCode()));
        $capturedAmount = Amount::fromInt(0, Currency::fromIsoCode($paymentDetails->getPaymentOutput()->getAmountOfMoney()->getCurrencyCode()));
        $captureRequestedAmount = Amount::fromInt(0, Currency::fromIsoCode($paymentDetails->getPaymentOutput()->getAmountOfMoney()->getCurrencyCode()));
        $cancelledAmount = Amount::fromInt(0, Currency::fromIsoCode($paymentDetails->getPaymentOutput()->getAmountOfMoney()->getCurrencyCode()));
        foreach ($paymentDetails->getOperations() as $operation) {
            if (in_array($operation->getStatusOutput()->getStatusCode(), StatusCode::REFUND_STATUS_CODES, \true)) {
                $refundedAmount = $refundedAmount->plus(Amount::fromInt($operation->getAmountOfMoney()->getAmount(), Currency::fromIsoCode($operation->getAmountOfMoney()->getCurrencyCode())));
            }
            if (in_array($operation->getStatusOutput()->getStatusCode(), StatusCode::REFUND_REQUESTED_STATUS_CODES, \true)) {
                $refundRequestedAmount = $refundRequestedAmount->plus(Amount::fromInt($operation->getAmountOfMoney()->getAmount(), Currency::fromIsoCode($operation->getAmountOfMoney()->getCurrencyCode())));
            }
            if (in_array($operation->getStatusOutput()->getStatusCode(), StatusCode::CAPTURE_STATUS_CODES, \true)) {
                $capturedAmount = $capturedAmount->plus(Amount::fromInt($operation->getAmountOfMoney()->getAmount(), Currency::fromIsoCode($operation->getAmountOfMoney()->getCurrencyCode())));
            }
            if (in_array($operation->getStatusOutput()->getStatusCode(), StatusCode::CAPTURE_REQUESTED_STATUS_CODES, \true)) {
                $captureRequestedAmount = $captureRequestedAmount->plus(Amount::fromInt($operation->getAmountOfMoney()->getAmount(), Currency::fromIsoCode($operation->getAmountOfMoney()->getCurrencyCode())));
            }
            if (in_array($operation->getStatusOutput()->getStatusCode(), StatusCode::CANCEL_STATUS_CODES, \true)) {
                $cancelledAmount = $cancelledAmount->plus(Amount::fromInt($operation->getAmountOfMoney()->getAmount(), Currency::fromIsoCode($operation->getAmountOfMoney()->getCurrencyCode())));
            }
        }
        if (empty($paymentDetails->getOperations())) {
            $capturedAmount = Amount::fromInt($paymentDetails->getPaymentOutput()->getAmountOfMoney()->getAmount(), Currency::fromIsoCode($paymentDetails->getPaymentOutput()->getAmountOfMoney()->getCurrencyCode()));
        }
        return new PaymentAmounts($refundedAmount, $refundRequestedAmount, $capturedAmount, $captureRequestedAmount, $cancelledAmount);
    }
    private static function isFullyPaid(PaymentOutput $paymentOutput): bool
    {
        $surchargeAmount = $paymentOutput->getSurchargeSpecificOutput() ? $paymentOutput->getSurchargeSpecificOutput()->getSurchargeAmount()->getAmount() : 0;
        $totalAmount = $paymentOutput->getAmountOfMoney()->getAmount() + $surchargeAmount;
        return $totalAmount === $paymentOutput->getAcquiredAmount()->getAmount();
    }
}
