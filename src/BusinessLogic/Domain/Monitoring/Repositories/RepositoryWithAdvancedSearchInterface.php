<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\Repositories;

use DateTime;
use WOP\OnlinePayments\Core\Bootstrap\DataAccess\Monitoring\MonitoringLog;
use WOP\OnlinePayments\Core\Bootstrap\DataAccess\Monitoring\WebhookLog;
/**
 * Interface RepositoryWithAdvancedSearchInterface
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Monitoring\Repositories
 */
interface RepositoryWithAdvancedSearchInterface
{
    /**
     * @param int $pageNumber
     * @param int $pageSize
     * @param string $searchTerm
     * @param DateTime|null $disconnectTime
     *
     * @return WebhookLog[]|MonitoringLog[]
     */
    public function getLogs(int $pageNumber, int $pageSize, string $searchTerm, ?DateTime $disconnectTime = null): array;
}
