<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Order\Proxies;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Refund\RefundRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Refund\RefundResponse;
/**
 * Interface RefundProxyInterface.
 *
 * @package OnlinePayments\Core\BusinessLogic\Order\Proxies
 */
interface RefundProxyInterface
{
    public function create(RefundRequest $refundRequest): RefundResponse;
}
