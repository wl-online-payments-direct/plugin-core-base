<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Order\Services\Refund;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Refund\RefundRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Refund\RefundResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Order\Proxies\RefundProxyInterface;
/**
 * Class RefundService
 *
 * @package OnlinePayments\Core\BusinessLogic\Order\Services\Refund
 */
class RefundService
{
    private RefundProxyInterface $refundProxy;
    public function __construct(RefundProxyInterface $refundProxy)
    {
        $this->refundProxy = $refundProxy;
    }
    public function handle(RefundRequest $request): RefundResponse
    {
        return $this->refundProxy->create($request);
    }
}
