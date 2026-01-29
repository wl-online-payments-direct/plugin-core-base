<?php

namespace WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Composite;

use WOP\OnlinePayments\Core\Infrastructure\Serializer\Interfaces\Serializable;
use WOP\OnlinePayments\Core\Infrastructure\Serializer\Serializer;
/**
 * Class ExecutionDetails
 *
 * @package OnlinePayments\Core\Infrastructure\TaskExecution\Composite
 *
 * @access private
 */
class ExecutionDetails implements Serializable
{
    /**
     * Execution id.
     *
     * @var int
     */
    private int $executionId;
    /**
     * Positive (grater than zero) integer. Higher number implies higher impact of subtask's progress on total progress.
     *
     * @var int
     */
    private int $weight;
    /**
     * Task progress.
     *
     * @var float
     */
    private float $progress;
    /**
     * ExecutionDetails constructor.
     *
     * @param int $executionId
     * @param int $weight
     */
    public function __construct(int $executionId, int $weight = 1)
    {
        $this->executionId = $executionId;
        $this->weight = $weight;
        $this->progress = 0.0;
    }
    /**
     * @return ?int
     */
    public function getExecutionId(): ?int
    {
        return $this->executionId;
    }
    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }
    /**
     * @return float
     */
    public function getProgress(): float
    {
        return $this->progress;
    }
    /**
     * @param float $progress
     */
    public function setProgress(float $progress)
    {
        $this->progress = $progress;
    }
    /**
     * @inheritDoc
     */
    public function serialize(): string
    {
        return Serializer::serialize([$this->executionId, $this->weight, $this->progress]);
    }
    /**
     * @inheritDoc
     */
    public function unserialize($data)
    {
        list($this->executionId, $this->weight, $this->progress) = Serializer::unserialize($data);
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return ['progress' => $this->getProgress(), 'executionId' => $this->getExecutionId(), 'weight' => $this->getWeight()];
    }
    /**
     * @inheritDoc
     */
    public static function fromArray(array $array): Serializable
    {
        $entity = new static($array['executionId'], $array['weight']);
        $entity->setProgress($array['progress']);
        return $entity;
    }
}
