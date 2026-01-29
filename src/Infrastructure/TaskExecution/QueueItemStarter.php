<?php

namespace WOP\OnlinePayments\Core\Infrastructure\TaskExecution;

use WOP\OnlinePayments\Core\Infrastructure\Configuration\ConfigurationManager;
use WOP\OnlinePayments\Core\Infrastructure\Logger\Logger;
use WOP\OnlinePayments\Core\Infrastructure\Serializer\Interfaces\Serializable;
use WOP\OnlinePayments\Core\Infrastructure\Serializer\Serializer;
use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Exceptions\AbortTaskExecutionException;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Exceptions\ExecutionRequirementsNotMetException;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Exceptions\QueueStorageUnavailableException;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Interfaces\Runnable;
use Exception;
/**
 * Class QueueItemStarter
 * @package OnlinePayments\Core\Infrastructure\TaskExecution
 */
class QueueItemStarter implements Runnable
{
    /**
     * Id of queue item to start.
     *
     * @var int
     */
    private int $queueItemId;
    /**
     * Service instance.
     *
     * @var ?QueueService
     */
    private ?QueueService $queueService = null;
    /**
     * Service instance.
     *
     * @var ?ConfigurationManager
     */
    private ?ConfigurationManager $configurationManager = null;
    /**
     * QueueItemStarter constructor.
     *
     * @param int $queueItemId Id of queue item to start.
     */
    public function __construct(int $queueItemId)
    {
        $this->queueItemId = $queueItemId;
    }
    /**
     * Transforms array into an serializable object,
     *
     * @param array $array Data that is used to instantiate serializable object.
     *
     * @return Serializable
     *      Instance of serialized object.
     */
    public static function fromArray(array $array): Serializable
    {
        return new static($array['queue_item_id']);
    }
    /**
     * Transforms serializable object into an array.
     *
     * @return array Array representation of a serializable object.
     */
    public function toArray(): array
    {
        return ['queue_item_id' => $this->queueItemId];
    }
    /**
     * @inheritdoc
     */
    public function serialize(): string
    {
        return Serializer::serialize([$this->queueItemId]);
    }
    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        list($this->queueItemId) = Serializer::unserialize($serialized);
    }
    /**
     * Starts runnable run logic.
     */
    public function run()
    {
        /** @var QueueItem $queueItem */
        $queueItem = $this->fetchItem();
        if ($queueItem === null || $queueItem->getStatus() !== QueueItem::QUEUED) {
            Logger::logDebug('Fail to start task execution because task no longer exists or it is not in queued state anymore.', 'Core', ['TaskId' => $this->getQueueItemId(), 'Status' => $queueItem !== null ? $queueItem->getStatus() : 'unknown']);
            return;
        }
        $queueService = $this->getQueueService();
        try {
            $this->getConfigManager()->setContext($queueItem->getContext());
            $queueService->validateExecutionRequirements($queueItem);
            $queueService->start($queueItem);
        } catch (QueueStorageUnavailableException $e) {
            Logger::logInfo($e->getMessage(), 'Core', ['trace' => $e->getTraceAsString()]);
        } catch (ExecutionRequirementsNotMetException $e) {
            $id = $queueItem->getId();
            Logger::logWarning("Execution requirements not met for queue item [{$id}] because:" . $e->getMessage(), 'Core', ['ExceptionTrace' => $e->getTraceAsString()]);
        } catch (AbortTaskExecutionException $exception) {
            $queueService->abort($queueItem, $exception->getMessage());
        } catch (Exception $ex) {
            if (QueueItem::IN_PROGRESS === $queueItem->getStatus()) {
                $queueService->fail($queueItem, $ex->getMessage());
            }
            $context = ['TaskId' => $this->getQueueItemId(), 'ExceptionMessage' => $ex->getMessage(), 'ExceptionTrace' => $ex->getTraceAsString()];
            Logger::logError("Fail to start task execution because: {$ex->getMessage()}.", 'Core', $context);
        }
    }
    /**
     * Gets id of a queue item that will be run.
     *
     * @return int Id of queue item to run.
     */
    public function getQueueItemId(): int
    {
        return $this->queueItemId;
    }
    /**
     * Gets Queue item.
     *
     * @return QueueItem|null Queue item if found; otherwise, null.
     */
    private function fetchItem(): ?QueueItem
    {
        try {
            $queueItem = $this->getQueueService()->find($this->queueItemId);
        } catch (Exception $ex) {
            return null;
        }
        return $queueItem;
    }
    /**
     * Gets Queue service instance.
     *
     * @return QueueService Service instance.
     */
    private function getQueueService(): QueueService
    {
        if ($this->queueService === null) {
            $this->queueService = ServiceRegister::getService(QueueService::CLASS_NAME);
        }
        return $this->queueService;
    }
    /**
     * Gets configuration service instance.
     *
     * @return ConfigurationManager Service instance.
     */
    private function getConfigManager(): ConfigurationManager
    {
        if ($this->configurationManager === null) {
            $this->configurationManager = ServiceRegister::getService(ConfigurationManager::CLASS_NAME);
        }
        return $this->configurationManager;
    }
}
