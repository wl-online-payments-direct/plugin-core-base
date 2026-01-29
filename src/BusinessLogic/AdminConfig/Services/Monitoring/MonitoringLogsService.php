<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Monitoring;

use Exception;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Disconnect\Repositories\DisconnectRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\MonitoringLog;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\Repositories\MonitoringLogRepositoryInterface;
/**
 * Class MonitoringLogsService
 *
 * @package OnlinePayments\Core\BusinessLogic\AdminConfig\Services\Monitoring
 */
class MonitoringLogsService
{
    protected MonitoringLogRepositoryInterface $monitoringLogRepository;
    protected DisconnectRepositoryInterface $repository;
    /**
     * @param MonitoringLogRepositoryInterface $monitoringLogRepository
     * @param DisconnectRepositoryInterface $repository
     */
    public function __construct(MonitoringLogRepositoryInterface $monitoringLogRepository, DisconnectRepositoryInterface $repository)
    {
        $this->monitoringLogRepository = $monitoringLogRepository;
        $this->repository = $repository;
    }
    /**
     * @param int $pageNumber
     * @param int $pageSize
     * @param string $searchTerm
     *
     * @return MonitoringLog[]
     *
     * @throws Exception
     */
    public function getLogs(int $pageNumber, int $pageSize, string $searchTerm): array
    {
        $disconnectTime = $this->repository->getDisconnectTime();
        return $this->monitoringLogRepository->getLogs($pageNumber, $pageSize, $searchTerm, $disconnectTime);
    }
    /**
     * @return array
     */
    public function getAllLogs(): array
    {
        $logs = $this->monitoringLogRepository->getAllLogs();
        $result = [];
        foreach ($logs as $log) {
            $result[] = $log->toArray();
        }
        return $result;
    }
    /**
     * @param string $searchTerm
     * @return int
     *
     * @throws Exception
     */
    public function count(string $searchTerm): int
    {
        $disconnectTime = $this->repository->getDisconnectTime();
        return $this->monitoringLogRepository->count($disconnectTime, $searchTerm);
    }
    /**
     * @param string $mode
     * @param int $limit
     *
     * @return void
     *
     * @throws Exception
     */
    public function delete(string $mode, int $limit = 5000): void
    {
        $disconnectTime = $this->repository->getDisconnectTime();
        $this->monitoringLogRepository->deleteByMode($disconnectTime, $mode, $limit);
    }
}
