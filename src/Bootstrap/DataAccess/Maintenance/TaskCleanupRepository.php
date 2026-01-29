<?php

namespace WOP\OnlinePayments\Core\Bootstrap\DataAccess\Maintenance;

use DateTime;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Interfaces\QueueItemRepository;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\Operators;
use WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter\QueryFilter;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\QueueItem;
/**
 * Class TaskCleanupRepository
 *
 * @package OnlinePayments\Core\Bootstrap\DataAccess\Maintenance
 */
class TaskCleanupRepository
{
    /**
     * @var QueueItemRepository
     */
    protected $repository;
    /**
     * @param QueueItemRepository $repository
     */
    public function __construct(QueueItemRepository $repository)
    {
        $this->repository = $repository;
    }
    public function getCompletedCount(): int
    {
        $filter = new QueryFilter();
        $filter->where('status', Operators::EQUALS, QueueItem::COMPLETED);
        return $this->repository->count($filter);
    }
    public function getFailedCount(): int
    {
        $filter = new QueryFilter();
        $filter->where('status', Operators::IN, [QueueItem::FAILED, QueueItem::ABORTED]);
        return $this->repository->count($filter);
    }
    public function deleteCompletedTasks(int $limit = 5000): void
    {
        $filter = new QueryFilter();
        $filter->where('status', Operators::EQUALS, QueueItem::COMPLETED);
        $filter->setLimit($limit);
        $this->repository->deleteWhere($filter);
    }
    public function deleteFailedTasks(DateTime $beforeDate, int $limit = 5000): void
    {
        $filter = new QueryFilter();
        $filter->where('status', Operators::IN, [QueueItem::FAILED, QueueItem::ABORTED])->where('queueTime', Operators::LESS_THAN, $beforeDate);
        $filter->setLimit($limit);
        $this->repository->deleteWhere($filter);
    }
}
