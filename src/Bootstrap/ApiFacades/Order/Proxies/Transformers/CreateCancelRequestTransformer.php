<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies\Transformers;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Cancel\CancelRequest;
use OnlinePayments\Sdk\Domain\AmountOfMoney;
use OnlinePayments\Sdk\Domain\CancelPaymentRequest;
/**
 * CreateCancelRequestTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies\Transformers
 */
class CreateCancelRequestTransformer
{
    public static function transform(CancelRequest $captureRequest): CancelPaymentRequest
    {
        $sdkRequest = new CancelPaymentRequest();
        $amountOfMoney = new AmountOfMoney();
        $amountOfMoney->setAmount($captureRequest->getAmount()->getValue());
        $amountOfMoney->setCurrencyCode($captureRequest->getAmount()->getCurrency()->getIsoCode());
        $sdkRequest->setAmountOfMoney($amountOfMoney);
        return $sdkRequest;
    }
}
