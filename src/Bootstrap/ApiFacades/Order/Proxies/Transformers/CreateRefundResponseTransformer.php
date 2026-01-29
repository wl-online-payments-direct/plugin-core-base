<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies\Transformers;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusCode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Refund\RefundResponse;
use OnlinePayments\Sdk\Domain\RefundResponse as SdkRefundResponse;
/**
 * CreateRefundResponseTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies\Transformers
 */
class CreateRefundResponseTransformer
{
    public static function transform(SdkRefundResponse $response): RefundResponse
    {
        return new RefundResponse(PaymentId::parse($response->id), StatusCode::parse((int) $response->getStatusOutput()->getStatusCode()), $response->getStatus());
    }
}
