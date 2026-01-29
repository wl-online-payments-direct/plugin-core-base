<?php

namespace WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter;

use DateTime;
use WOP\OnlinePayments\Core\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
/**
 * Class QueryFilter.
 *
 * @package OnlinePayments\Core\Infrastructure\ORM\QueryFilter
 */
class QueryFilter
{
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';
    /**
     * List of filter conditions.
     *
     * @var QueryCondition[]
     */
    private array $conditions = [];
    /**
     * Order by column name.
     *
     * @var ?string
     */
    private ?string $orderByColumn = null;
    /**
     * Order direction.
     *
     * @var string
     */
    private string $orderDirection = 'ASC';
    /**
     * Limit for select.
     *
     * @var ?int
     */
    private ?int $limit = null;
    /**
     * Offset for select.
     *
     * @var ?int
     */
    private ?int $offset = null;
    /**
     * Gets limit for select.
     *
     * @return int|null Limit for select.
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }
    /**
     * Sets limit for select.
     *
     * @param int $limit Limit for select.
     *
     * @return self This instance for chaining.
     */
    public function setLimit(int $limit): QueryFilter
    {
        $this->limit = $limit;
        return $this;
    }
    /**
     * Gets select offset.
     *
     * @return int|null Offset.
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }
    /**
     * Sets select offset.
     *
     * @param int $offset Offset.
     *
     * @return self This instance for chaining.
     */
    public function setOffset(int $offset): QueryFilter
    {
        $this->offset = $offset;
        return $this;
    }
    /**
     * Sets order by column and direction
     *
     * @param string $column Column name.
     * @param string $direction Order direction (@see self::ORDER_ASC or @see self::ORDER_DESC).
     *
     * @return self This instance for chaining.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function orderBy(string $column, string $direction = self::ORDER_ASC): QueryFilter
    {
        if (!in_array($direction, [self::ORDER_ASC, self::ORDER_DESC])) {
            throw new QueryFilterInvalidParamException('Column value must be string type and direction must be ASC or DESC');
        }
        $this->orderByColumn = $column;
        $this->orderDirection = $direction;
        return $this;
    }
    /**
     * Gets name for order by column.
     *
     * @return string|null Order column name.
     */
    public function getOrderByColumn(): ?string
    {
        return $this->orderByColumn;
    }
    /**
     * Gets order direction.
     *
     * @return string Order direction (@see self::ORDER_ASC or @see self::ORDER_DESC)
     */
    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }
    /**
     * Gets all conditions for this filter.
     *
     * @return QueryCondition[] Filter conditions.
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }
    /**
     * Sets where condition, if chained AND operator will be used
     *
     * @param string $column Column name.
     * @param string $operator Operator. Use constants from @see Operator class.
     * @param mixed $value Value of condition.
     *
     * @return self This instance for chaining.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function where(string $column, string $operator, $value = null): QueryFilter
    {
        $this->validateConditionParameters($column, $operator, $value);
        $this->conditions[] = new QueryCondition('AND', $column, $operator, $value);
        return $this;
    }
    /**
     * Sets where condition, if chained OR operator will be used.
     *
     * @param string $column Column name.
     * @param string $operator Operator. Use constants from @see Operator class.
     * @param mixed $value Value of condition.
     *
     * @return self This instance for chaining.
     *
     * @throws QueryFilterInvalidParamException
     */
    public function orWhere(string $column, string $operator, $value = null): QueryFilter
    {
        $this->validateConditionParameters($column, $operator, $value);
        $this->conditions[] = new QueryCondition('OR', $column, $operator, $value);
        return $this;
    }
    /**
     * Validates condition parameters.
     *
     * @param string $column Column name.
     * @param string $operator Operator. Use constants from @see Operator class.
     * @param mixed $value Value of condition.
     *
     * @throws QueryFilterInvalidParamException
     */
    private function validateConditionParameters(string $column, string $operator, $value)
    {
        $operator = strtoupper($operator);
        if (!in_array($operator, Operators::$AVAILABLE_OPERATORS, \true)) {
            throw new QueryFilterInvalidParamException("Operator {$operator} is not supported");
        }
        $valueType = gettype($value);
        if ($valueType === 'object' && $value instanceof DateTime) {
            $valueType = 'dateTime';
        }
        if (!array_key_exists($valueType, Operators::$TYPE_OPERATORS)) {
            throw new QueryFilterInvalidParamException('Value type is not supported');
        }
        if (!in_array($operator, Operators::$TYPE_OPERATORS[$valueType], \true)) {
            throw new QueryFilterInvalidParamException("Operator {$operator} is not supported for {$valueType} type");
        }
    }
}
