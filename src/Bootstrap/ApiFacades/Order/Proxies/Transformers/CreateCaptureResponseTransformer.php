<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies\Transformers;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Capture\CaptureResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\PaymentId;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Payment\StatusCode;
use OnlinePayments\Sdk\Domain\CaptureResponse as SdkCaptureResponse;
/**
 * CreateCaptureResponseTransformer.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies\Transformers
 */
class CreateCaptureResponseTransformer
{
    public static function transform(SdkCaptureResponse $response): CaptureResponse
    {
        return new CaptureResponse(PaymentId::parse($response->id), StatusCode::parse((int) $response->getStatusOutput()->getStatusCode()), $response->getStatus());
    }
}
