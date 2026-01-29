<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Order\ApiFacades\RefundAPI\Controller;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Refund\RefundRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Order\ApiFacades\RefundAPI\Response\CreateRefundResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Order\Services\Refund\RefundService;
/**
 * Class RefundController
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\RefundAPI\Controller
 */
class RefundController
{
    private RefundService $refundService;
    /**
     * @param RefundService $refundService
     */
    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
    }
    public function handle(RefundRequest $request): CreateRefundResponse
    {
        return new CreateRefundResponse($this->refundService->handle($request));
    }
}
