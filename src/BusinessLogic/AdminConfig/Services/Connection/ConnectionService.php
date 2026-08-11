<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Connection;

use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Connection\Proxies\ConnectionProxyInterface;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck\HealthCheckResult;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\HealthCheck\HealthCheckService;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionDetails;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Exceptions\InvalidConnectionDetailsException;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Repositories\ConnectionConfigRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Translations\Model\TranslatableLabel;
use WOP\OnlinePayments\Core\Infrastructure\Configuration\Configuration;
/**
 * Class ConnectionService
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Connection
 */
class ConnectionService
{
    protected ConnectionConfigRepositoryInterface $connectionConfigRepository;
    protected ConnectionProxyInterface $proxy;
    private HealthCheckService $healthCheckService;
    private Configuration $configuration;
    /**
     * @param ConnectionConfigRepositoryInterface $connectionConfigRepository
     * @param ConnectionProxyInterface $proxy
     * @param HealthCheckService $healthCheckService
     * @param Configuration $configuration
     */
    public function __construct(ConnectionConfigRepositoryInterface $connectionConfigRepository, ConnectionProxyInterface $proxy, HealthCheckService $healthCheckService, Configuration $configuration)
    {
        $this->connectionConfigRepository = $connectionConfigRepository;
        $this->proxy = $proxy;
        $this->healthCheckService = $healthCheckService;
        $this->configuration = $configuration;
    }
    /**
     * Runs the sequential Payment API / webhook credential / mock webhook health check for the given
     * connection details, and saves them only if the result allows it (see HealthCheckResult::isSaveable()) -
     * i.e. a failed mock webhook test alone does not block saving, only the first two steps do.
     *
     * @param ConnectionDetails $connectionDetails
     *
     * @return HealthCheckResult
     */
    public function connect(ConnectionDetails $connectionDetails): HealthCheckResult
    {
        $result = $this->healthCheckService->check($connectionDetails, $this->configuration->getWebhookUrl());
        if ($result->isSaveable()) {
            $this->connectionConfigRepository->saveConnection($connectionDetails);
        }
        return $result;
    }
    /**
     * Runs the same three checks against the already-saved connection details and persists nothing.
     * Backs the "Check Credentials" action, which the admin offers when a saved configuration is loaded
     * and unmodified, so there is nothing to write back.
     *
     * @return HealthCheckResult
     *
     * @throws InvalidConnectionDetailsException When no connection has been saved for this store yet.
     */
    public function checkSavedConnection(): HealthCheckResult
    {
        $connectionDetails = $this->connectionConfigRepository->getConnection();
        if (null === $connectionDetails) {
            throw new InvalidConnectionDetailsException(new TranslatableLabel('No saved connection to check.', 'connection.noSavedConnection'));
        }
        return $this->healthCheckService->check($connectionDetails, $this->configuration->getWebhookUrl());
    }
    /**
     * Check if user is logged in.
     *
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        $connectionDetails = $this->getConnectionConfig();
        try {
            if ($connectionDetails) {
                $this->validateConnectionDetails($connectionDetails);
                return \true;
            }
        } catch (InvalidConnectionDetailsException $e) {
            // intentionally left empty
        }
        return \false;
    }
    /**
     * Retrieves saved connection config.
     *
     * @return ConnectionDetails|null
     */
    public function getConnectionConfig(): ?ConnectionDetails
    {
        return $this->connectionConfigRepository->getConnection();
    }
    /**
     * @param ConnectionDetails $connectionDetails
     *
     * @return void
     *
     * @throws InvalidConnectionDetailsException
     */
    protected function validateConnectionDetails(ConnectionDetails $connectionDetails): void
    {
        $this->validate($connectionDetails);
    }
    /**
     * @throws InvalidConnectionDetailsException
     */
    protected function validate(ConnectionDetails $connectionDetails): void
    {
        $isValid = $this->proxy->isConnectionValid($connectionDetails);
        if (!$isValid) {
            throw new InvalidConnectionDetailsException(new TranslatableLabel('Invalid connection details.', 'connection.apiValidationFailed'));
        }
    }
}
