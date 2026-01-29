<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies;

use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies\Transformers\CreateCancelRequestTransformer;
use WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies\Transformers\CreateCancelResponseTransformer;
use WOP\OnlinePayments\Core\Bootstrap\Sdk\MerchantClientFactory;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Cancel\CancelRequest;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Cancel\CancelResponse;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\ContextLogProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Order\Proxies\CancelProxyInterface;
/**
 * CancelProxy.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\Order\Proxies
 */
class CancelProxy implements CancelProxyInterface
{
    private MerchantClientFactory $clientFactory;
    public function __construct(MerchantClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }
    public function create(CancelRequest $cancelRequest): CancelResponse
    {
        ContextLogProvider::getInstance()->setPaymentNumber($cancelRequest->getPaymentId()->getTransactionId());
        return CreateCancelResponseTransformer::transform($this->clientFactory->get()->payments()->cancelPayment((string) $cancelRequest->getPaymentId(), CreateCancelRequestTransformer::transform($cancelRequest)));
    }
}
