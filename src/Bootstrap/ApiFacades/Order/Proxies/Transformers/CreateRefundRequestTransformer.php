<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies\Transformers;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Refund\RefundRequest;
use OnlinePayments\Sdk\Domain\AmountOfMoney;
use OnlinePayments\Sdk\Domain\OperationPaymentReferences;
use OnlinePayments\Sdk\Domain\RefundRequest as SdkRefundRequest;
/**
 * CreateRefundRequestTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies\Transformers
 */
class CreateRefundRequestTransformer
{
    public static function transform(RefundRequest $refundRequest): SdkRefundRequest
    {
        $sdkRequest = new SdkRefundRequest();
        $amountOfMoney = new AmountOfMoney();
        $amountOfMoney->setAmount($refundRequest->getAmount()->getValue());
        $amountOfMoney->setCurrencyCode($refundRequest->getAmount()->getCurrency()->getIsoCode());
        $sdkRequest->setAmountOfMoney($amountOfMoney);
        if ($refundRequest->getMerchantReference()) {
            $references = new OperationPaymentReferences();
            $references->setMerchantReference($refundRequest->getMerchantReference());
            $sdkRequest->setOperationReferences($references);
        }
        return $sdkRequest;
    }
}
