<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\Monitoring;

use DateInterval;
use DateTime;
use Exception;
use WOP\OnlinePayments\Core\Bootstrap\DataAccess\Monitoring\MonitoringLog as MonitoringLogEntity;
use WOP\OnlinePayments\Core\Branding\Brand\ActiveBrandProviderInterface;
use WOP\OnlinePayments\Core\BusinessLogic\AdminConfig\Services\GeneralSettings\Repositories\LogSettingsRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ActiveConnectionProvider;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Connection\ConnectionMode;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\MonitoringLog;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\Repositories\MonitoringLogRepositoryInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Monitoring\Repositories\RepositoryWithAdvancedSearchInterface;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Exceptions\EntityClassException;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\Operators;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\QueryFilter;
/**
 * Class MonitoringLogRepository
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\Monitoring
 */
class MonitoringLogRepository implements MonitoringLogRepositoryInterface
{
    protected RepositoryWithAdvancedSearchInterface $repository;
    protected StoreContext $storeContext;
    protected ActiveConnectionProvider $activeConnectionProvider;
    protected ActiveBrandProviderInterface $activeBrandProvider;
    protected LogSettingsRepositoryInterface $logSettingsRepository;
    /**
     * @param RepositoryWithAdvancedSearchInterface $repository
     * @param StoreContext $storeContext
     * @param ActiveConnectionProvider $activeConnectionProvider
     * @param ActiveBrandProviderInterface $activeBrandProvider
     * @param LogSettingsRepositoryInterface $logSettingsRepository
     */
    public function __construct(RepositoryWithAdvancedSearchInterface $repository, StoreContext $storeContext, ActiveConnectionProvider $activeConnectionProvider, ActiveBrandProviderInterface $activeBrandProvider, LogSettingsRepositoryInterface $logSettingsRepository)
    {
        $this->repository = $repository;
        $this->storeContext = $storeContext;
        $this->activeConnectionProvider = $activeConnectionProvider;
        $this->activeBrandProvider = $activeBrandProvider;
        $this->logSettingsRepository = $logSettingsRepository;
    }
    /**
     * @param MonitoringLog $monitoringLog
     *
     * @return void
     *
     * @throws Exception
     */
    public function saveMonitoringLog(MonitoringLog $monitoringLog): void
    {
        $brand = $this->activeBrandProvider->getActiveBrand();
        $mode = str_contains($monitoringLog->getRequestEndpoint(), $brand->getLiveApiEndpoint()) !== \false ? ConnectionMode::live() : ConnectionMode::test();
        $activeConnection = $this->activeConnectionProvider->get();
        if ($activeConnection !== null) {
            $mode = $activeConnection->getMode();
        }
        $existingLog = $this->getLogByRequestId($monitoringLog->getRequestId());
        if ($existingLog) {
            $monitoringLog->setRequestBody($existingLog->getMonitoringLog()->getRequestBody());
            $monitoringLog->setMessage($existingLog->getMonitoringLog()->getMessage());
            $monitoringLog->setCreatedAt($existingLog->getMonitoringLog()->getCreatedAt());
        }
        $logSettings = $this->logSettingsRepository->getLogSettings();
        $createdAtDate = clone $monitoringLog->getCreatedAt();
        $createdAt = $monitoringLog->getCreatedAt()->getTimestamp();
        $expiresAt = (clone $monitoringLog->getCreatedAt())->add(new DateInterval('P14D'))->getTimestamp();
        if ($logSettings) {
            $expiresAt = $createdAtDate->add(new DateInterval('P' . $logSettings->getLogRecordsLifetime()->getDays() . 'D'))->getTimestamp();
        }
        $monitoringLog->setOrderLink($this->getOrderUrl($monitoringLog));
        $monitoringLog->setOrderId($this->getOrderIdByMerchantReference($monitoringLog->getOrderId()));
        $entity = new MonitoringLogEntity();
        $entity->setStoreId($this->storeContext->getStoreId());
        $entity->setMode((string) $mode);
        $entity->setOrderId($monitoringLog->getOrderId());
        $entity->setRequestId($monitoringLog->getRequestId());
        $entity->setPaymentNumber($monitoringLog->getPaymentNumber());
        $entity->setCreatedAt($createdAt);
        $entity->setExpiresAt($expiresAt);
        $entity->setMessage($monitoringLog->getMessage());
        $entity->setMonitoringLog($monitoringLog);
        if ($existingLog) {
            $entity->setId($existingLog->getId());
            $this->repository->update($entity);
            return;
        }
        $this->repository->save($entity);
    }
    /**
     *
     * @param int $pageNumber
     * @param int $pageSize
     * @param string $searchTerm
     * @param DateTime|null $disconnectTime
     *
     * @return array
     */
    public function getLogs(int $pageNumber, int $pageSize, string $searchTerm, ?DateTime $disconnectTime = null): array
    {
        $activeConnection = $this->activeConnectionProvider->get();
        if (null === $activeConnection) {
            return [];
        }
        /** @var MonitoringLogEntity[] $entities */
        $entities = $this->repository->getLogs($pageNumber, $pageSize, $searchTerm, $disconnectTime);
        $result = [];
        foreach ($entities as $entity) {
            $result[] = $entity->getMonitoringLog();
        }
        return $result;
    }
    /**
     * @return array
     *
     * @throws QueryFilterInvalidParamException
     */
    public function getAllLogs(): array
    {
        $activeConnection = $this->activeConnectionProvider->get();
        if (null === $activeConnection) {
            return [];
        }
        $queryFilter = new QueryFilter();
        $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId())->where('mode', Operators::EQUALS, (string) $activeConnection->getMode());
        /** @var MonitoringLogEntity[] $entities */
        $entities = $this->repository->select($queryFilter);
        $result = [];
        foreach ($entities as $entity) {
            $result[] = $entity->getMonitoringLog();
        }
        return $result;
    }
    /**
     * @param DateTime|null $disconnectTime
     * @param string $searchTerm
     *
     * @return int
     *
     * @throws QueryFilterInvalidParamException
     * @throws EntityClassException
     */
    public function count(?DateTime $disconnectTime = null, string $searchTerm = ''): int
    {
        $activeConnection = $this->activeConnectionProvider->get();
        if (null === $activeConnection) {
            return 0;
        }
        $queryFilter = new QueryFilter();
        $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId())->where('mode', Operators::EQUALS, (string) $activeConnection->getMode());
        if ($disconnectTime) {
            $queryFilter->where('createdAt', Operators::LESS_THAN, $disconnectTime->getTimestamp());
        }
        if ($searchTerm) {
            $queryFilter->where('orderId', Operators::LIKE, '%' . $searchTerm . '%')->where('paymentNumber', Operators::LIKE, '%' . $searchTerm . '%')->where('message', Operators::LIKE, '%' . $searchTerm . '%');
        }
        return $this->repository->count($queryFilter);
    }
    /**
     * @param DateTime $beforeDate
     * @param string $mode
     * @param int $limit
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function deleteByMode(DateTime $beforeDate, string $mode, int $limit): void
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('storeId', Operators::EQUALS, $this->storeContext->getStoreId())->where('mode', Operators::EQUALS, $mode)->where('createdAt', Operators::LESS_THAN, $beforeDate->getTimestamp())->setLimit($limit);
        $this->repository->deleteWhere($queryFilter);
    }
    /**
     * @return int
     *
     * @throws QueryFilterInvalidParamException
     */
    public function countExpired(): int
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('expiresAt', Operators::LESS_THAN, (new DateTime())->getTimestamp());
        return $this->repository->count($queryFilter);
    }
    /**
     * @param int $limit
     *
     * @return void
     *
     * @throws QueryFilterInvalidParamException
     */
    public function deleteExpired(int $limit = 5000): void
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('expiresAt', Operators::LESS_THAN, (new DateTime())->getTimestamp())->setLimit($limit);
        $this->repository->deleteWhere($queryFilter);
    }
    /**
     * @param MonitoringLog $monitoringLog
     *
     * @return string
     */
    public function getOrderUrl(MonitoringLog $monitoringLog): string
    {
        return '';
    }
    /**
     * @param string $merchantReference
     *
     * @return string
     */
    public function getOrderIdByMerchantReference(string $merchantReference): string
    {
        return $merchantReference;
    }
    protected function getLogByRequestId(string $requestId): ?MonitoringLogEntity
    {
        $queryFilter = new QueryFilter();
        $queryFilter->where('requestId', Operators::EQUALS, $requestId);
        return $this->repository->selectOne($queryFilter);
    }
}
