<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Order\Services\Cancel;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Cancel\CancelRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Cancel\CancelResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Order\Proxies\CancelProxyInterface;
/**
 * Class CancelService
 *
 * @package OnlinePayments\Core\BusinessLogic\Order\Services\Cancel
 */
class CancelService
{
    private CancelProxyInterface $cancelProxy;
    public function __construct(CancelProxyInterface $cancelProxy)
    {
        $this->cancelProxy = $cancelProxy;
    }
    public function handle(CancelRequest $request): CancelResponse
    {
        return $this->cancelProxy->create($request);
    }
}
