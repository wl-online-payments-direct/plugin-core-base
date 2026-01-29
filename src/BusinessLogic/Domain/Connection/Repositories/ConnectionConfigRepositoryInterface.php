<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Repositories;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionDetails;
/**
 * Interface ConnectionConfigRepositoryInterface.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Connection\Repositories
 */
interface ConnectionConfigRepositoryInterface
{
    /**
     * Saves connection details.
     *
     * @param ConnectionDetails $connectionDetails
     *
     * @return void
     */
    public function saveConnection(ConnectionDetails $connectionDetails): void;
    /**
     * Retrieves connection details.
     *
     * @return ConnectionDetails|null
     */
    public function getConnection(): ?ConnectionDetails;
    /**
     * Retrieves oldest connection details.
     *
     * @return ConnectionDetails|null
     */
    public function getOldestConnection(): ?ConnectionDetails;
    /**
     * Retrieves oldest connected store.
     *
     * @return string
     */
    public function getOldestConnectedStore(): string;
    /**
     * Disconnects currently active connection.
     *
     * @return void
     */
    public function disconnect(): void;
}
