<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Order\ApiFacades\OrdersAPI\Controller;

use WOP\OnlinePayments\Core\BusinessLogic\Order\ApiFacades\OrdersAPI\Response\OrderDetailsResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Order\Services\Order\OrderService;
/**
 * Class OrderController
 *
 * @package OnlinePayments\Core\BusinessLogic\Order\ApiFacades\OrdersAPI\Controller
 */
class OrderController
{
    private OrderService $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    public function getDetails(string $merchantReference): OrderDetailsResponse
    {
        return new OrderDetailsResponse($this->orderService->getDetails($merchantReference));
    }
}
