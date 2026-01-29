<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\LogCleanup;

/**
 * Class LogCleanupListener
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\LogCleanup
 */
class LogCleanupListener
{
    protected LogCleanupTaskServiceInterface $service;
    /**
     * @param LogCleanupTaskServiceInterface $service
     */
    public function __construct(LogCleanupTaskServiceInterface $service)
    {
        $this->service = $service;
    }
    /**
     * @return void
     */
    public function handle(): void
    {
        if (!$this->canHandle()) {
            return;
        }
        $this->doHandle();
    }
    protected function canHandle(): bool
    {
        $lastExecutionTime = $this->service->findLatestExecutionTimestamp();
        return $lastExecutionTime < (new \DateTime())->sub(new \DateInterval('P1D'))->getTimestamp();
    }
    protected function doHandle(): void
    {
        $this->service->enqueueLogCleanupTask();
    }
}
