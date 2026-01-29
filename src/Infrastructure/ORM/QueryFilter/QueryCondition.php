<?php

namespace WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter;

use DateTime;
/**
 * Class QueryCondition.
 *
 * @package OnlinePayments\Core\Infrastructure\ORM\QueryFilter
 */
class QueryCondition
{
    /**
     * @var string - AND | OR
     */
    private string $chainOperator;
    /**
     * @var string
     */
    private string $column;
    /**
     * @var string
     */
    private string $operator;
    /**
     * @var mixed
     */
    private $value;
    /**
     * @var string
     */
    private string $valueType;
    /**
     * Condition constructor.
     *
     * @param string $chainOperator
     * @param string $column
     * @param string $operator
     * @param mixed $value
     */
    public function __construct(string $chainOperator, string $column, string $operator, $value)
    {
        $this->chainOperator = $chainOperator;
        $this->column = $column;
        $this->operator = $operator;
        $this->value = $value;
        $this->valueType = gettype($value);
        if ($this->valueType === 'object' && $value instanceof DateTime) {
            $this->valueType = 'dateTime';
        }
    }
    /**
     * @return string
     */
    public function getChainOperator(): string
    {
        return $this->chainOperator;
    }
    /**
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }
    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }
    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @return string
     */
    public function getValueType(): string
    {
        return $this->valueType;
    }
}
