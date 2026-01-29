<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies\Transformers;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Capture\CaptureRequest;
use OnlinePayments\Sdk\Domain\CapturePaymentRequest;
use OnlinePayments\Sdk\Domain\OperationPaymentReferences;
/**
 * CreateCaptureRequestTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies\Transformers
 */
class CreateCaptureRequestTransformer
{
    public static function transform(CaptureRequest $captureRequest): CapturePaymentRequest
    {
        $sdkRequest = new CapturePaymentRequest();
        if ($captureRequest->getAmount()) {
            $sdkRequest->setAmount($captureRequest->getAmount()->getValue());
        }
        if ($captureRequest->getMerchantReference()) {
            $references = new OperationPaymentReferences();
            $references->setMerchantReference($captureRequest->getMerchantReference());
            $sdkRequest->setOperationReferences($references);
        }
        return $sdkRequest;
    }
}
