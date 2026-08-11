<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\AdminConfig\Proxies;

use WOP\OnlinePayments\Core\Bootstrap\Sdk\MerchantClientFactory;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Connection\Proxies\ConnectionProxyInterface as BaseConnectionProxy;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck\HealthCheckService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionDetails;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Exceptions\InvalidConnectionDetailsException;
class ConnectionProxy implements BaseConnectionProxy
{
    private const CONNECTION_VALID = 'OK';
    private MerchantClientFactory $clientFactory;
    public function __construct(MerchantClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }
    /**
     * @inheritDoc
     *
     * @throws InvalidConnectionDetailsException
     */
    public function isConnectionValid(ConnectionDetails $connectionDetails): bool
    {
        $client = $this->clientFactory->get($connectionDetails, HealthCheckService::REQUEST_TIMEOUT_SECONDS);
        return $client->services()->testConnection()->getResult() === self::CONNECTION_VALID;
    }
}
