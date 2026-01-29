<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\Repositories\ConnectionConfigRepositoryInterface;
/**
 * Class ActiveConnectionProvider.
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Connection
 */
class ActiveConnectionProvider
{
    private ?ConnectionDetails $activeConnection = null;
    private ConnectionConfigRepositoryInterface $connectionConfigRepository;
    public function __construct(ConnectionConfigRepositoryInterface $connectionConfigRepository)
    {
        $this->connectionConfigRepository = $connectionConfigRepository;
    }
    /**
     * Gets active connection details or null if connection is not configured
     *
     * @return ConnectionDetails|null
     */
    public function get(): ?ConnectionDetails
    {
        if (null === $this->activeConnection) {
            $this->activeConnection = $this->connectionConfigRepository->getConnection();
        }
        return $this->activeConnection;
    }
}
