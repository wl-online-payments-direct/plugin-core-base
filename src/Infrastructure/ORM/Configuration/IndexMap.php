<?php

namespace WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration;

/**
 * Class IndexMap.
 *
 * @package OnlinePayments\Core\Infrastructure\ORM\Configuration
 */
class IndexMap
{
    /**
     * Array of indexed columns.
     *
     * @var IndexColumn[]
     */
    private array $indexes = [];
    /**
     * Adds boolean index.
     *
     * @param string $name Column name for index.
     *
     * @return self This instance for chaining.
     */
    public function addBooleanIndex(string $name): IndexMap
    {
        return $this->addIndex(new IndexColumn(IndexColumn::BOOLEAN, $name));
    }
    /**
     * Adds datetime index.
     *
     * @param string $name Column name for index.
     *
     * @return self This instance for chaining.
     */
    public function addDateTimeIndex(string $name): IndexMap
    {
        return $this->addIndex(new IndexColumn(IndexColumn::DATETIME, $name));
    }
    /**
     * Adds double index.
     *
     * @param string $name Column name for index.
     *
     * @return self This instance for chaining.
     */
    public function addDoubleIndex(string $name): IndexMap
    {
        return $this->addIndex(new IndexColumn(IndexColumn::DOUBLE, $name));
    }
    /**
     * Adds integer index.
     *
     * @param string $name Column name for index.
     *
     * @return self This instance for chaining.
     */
    public function addIntegerIndex(string $name): IndexMap
    {
        return $this->addIndex(new IndexColumn(IndexColumn::INTEGER, $name));
    }
    /**
     * Adds string index.
     *
     * @param string $name Column name for index.
     *
     * @return self This instance for chaining.
     */
    public function addStringIndex(string $name): IndexMap
    {
        return $this->addIndex(new IndexColumn(IndexColumn::STRING, $name));
    }
    /**
     * Returns array of indexes.
     *
     * @return IndexColumn[] Array of indexes.
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }
    /**
     * Adds index to map.
     *
     * @param IndexColumn $index Index to be added.
     *
     * @return self This instance for chaining.
     */
    protected function addIndex(IndexColumn $index): IndexMap
    {
        $this->indexes[$index->getProperty()] = $index;
        return $this;
    }
}
