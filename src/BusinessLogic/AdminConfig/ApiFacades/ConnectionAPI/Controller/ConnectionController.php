<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ConnectionAPI\Controller;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ConnectionAPI\Request\ConnectionRequest;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ConnectionAPI\Response\ConnectionConfigResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ConnectionAPI\Response\ConnectionResponse;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Connection\ConnectionService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Exceptions\InvalidConnectionDetailsException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Exceptions\InvalidConnectionModeException;
/**
 * Class ConnectionController
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\ApiFacades\ConnectionAPI\Controller
 */
class ConnectionController
{
    protected ConnectionService $connectionService;
    public function __construct(ConnectionService $connectionService)
    {
        $this->connectionService = $connectionService;
    }
    /**
     * @param ConnectionRequest $connectionRequest
     *
     * @return ConnectionResponse
     *
     * @throws InvalidConnectionDetailsException When the request itself is missing credentials for its mode.
     * @throws InvalidConnectionModeException
     */
    public function connect(ConnectionRequest $connectionRequest): ConnectionResponse
    {
        $result = $this->connectionService->connect($connectionRequest->transformToDomainModel());
        return new ConnectionResponse($result, $result->isSaveable());
    }
    /**
     * Validates the already-saved connection details without writing anything back. Backs the
     * "Check Credentials" action offered for a loaded, unmodified configuration.
     *
     * @return ConnectionResponse
     *
     * @throws InvalidConnectionDetailsException When no connection has been saved for this store yet.
     */
    public function checkCredentials(): ConnectionResponse
    {
        return new ConnectionResponse($this->connectionService->checkSavedConnection(), \false);
    }
    /**
     * @return ConnectionConfigResponse
     */
    public function getConnectionConfig(): ConnectionConfigResponse
    {
        return new ConnectionConfigResponse($this->connectionService->getConnectionConfig());
    }
}
