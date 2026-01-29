<?php

namespace WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration;

/**
 * Class EntityConfiguration.
 *
 * @package OnlinePayments\Core\Infrastructure\ORM\Configuration
 */
class EntityConfiguration
{
    /**
     * Index map.
     *
     * @var IndexMap
     */
    private IndexMap $indexMap;
    /**
     * Entity type.
     *
     * @var string
     */
    private string $type;
    /**
     * EntityConfiguration constructor.
     *
     * @param IndexMap $indexMap Index map object.
     * @param string $type Entity unique type.
     */
    public function __construct(IndexMap $indexMap, string $type)
    {
        $this->indexMap = $indexMap;
        $this->type = $type;
    }
    /**
     * Returns index map.
     *
     * @return IndexMap Index map.
     */
    public function getIndexMap(): IndexMap
    {
        return $this->indexMap;
    }
    /**
     * Returns type.
     *
     * @return string Entity type.
     */
    public function getType(): string
    {
        return $this->type;
    }
}
