<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies\Transformers\CreateRefundRequestTransformer;
use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies\Transformers\CreateRefundResponseTransformer;
use WOP\OnlinePayments\Core\Bootstrap\Sdk\MerchantClientFactory;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\ContextLogProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Refund\RefundRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Refund\RefundResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Order\Proxies\RefundProxyInterface;
/**
 * RefundProxy.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies
 */
class RefundProxy implements RefundProxyInterface
{
    private MerchantClientFactory $clientFactory;
    public function __construct(MerchantClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }
    public function create(RefundRequest $refundRequest): RefundResponse
    {
        ContextLogProvider::getInstance()->setPaymentNumber($refundRequest->getPaymentId()->getTransactionId());
        return CreateRefundResponseTransformer::transform($this->clientFactory->get()->payments()->refundPayment((string) $refundRequest->getPaymentId(), CreateRefundRequestTransformer::transform($refundRequest)));
    }
}
