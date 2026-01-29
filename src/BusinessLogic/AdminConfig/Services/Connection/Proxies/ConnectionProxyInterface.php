<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Connection\Proxies;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionDetails;
/**
 * Interface ConnectionProxy
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Connection\Proxies
 */
interface ConnectionProxyInterface
{
    /**
     * Tests if connection is valid.
     *
     * @param ConnectionDetails $connectionDetails
     *
     * @return bool
     */
    public function isConnectionValid(ConnectionDetails $connectionDetails): bool;
}
