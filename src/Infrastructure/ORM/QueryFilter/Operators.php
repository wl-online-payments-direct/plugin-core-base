<?php

namespace WOP\OnlinePayments\Core\Infrastructure\ORM\QueryFilter;

/**
 * Class Operators.
 *
 * @package OnlinePayments\Core\Infrastructure\ORM\QueryFilter
 */
class Operators
{
    const EQUALS = '=';
    const NOT_EQUALS = '!=';
    const GREATER_THAN = '>';
    const GREATER_OR_EQUAL_THAN = '>=';
    const LESS_THAN = '<';
    const LESS_OR_EQUAL_THAN = '<=';
    const LIKE = 'LIKE';
    const IN = 'IN';
    const NOT_IN = 'NOT IN';
    const NULL = 'IS NULL';
    const NOT_NULL = 'IS NOT NULL';
    public static $AVAILABLE_OPERATORS = [self::EQUALS, self::NOT_EQUALS, self::GREATER_THAN, self::GREATER_OR_EQUAL_THAN, self::LESS_THAN, self::LESS_OR_EQUAL_THAN, self::LIKE, self::IN, self::NOT_IN, self::NULL, self::NOT_NULL];
    public static $TYPE_OPERATORS = ['integer' => [self::EQUALS, self::NOT_EQUALS, self::GREATER_THAN, self::GREATER_OR_EQUAL_THAN, self::LESS_THAN, self::LESS_OR_EQUAL_THAN], 'double' => [self::EQUALS, self::NOT_EQUALS, self::GREATER_THAN, self::GREATER_OR_EQUAL_THAN, self::LESS_THAN, self::LESS_OR_EQUAL_THAN], 'dateTime' => [self::EQUALS, self::NOT_EQUALS, self::GREATER_THAN, self::GREATER_OR_EQUAL_THAN, self::LESS_THAN, self::LESS_OR_EQUAL_THAN], 'string' => [self::EQUALS, self::NOT_EQUALS, self::GREATER_THAN, self::GREATER_OR_EQUAL_THAN, self::LESS_THAN, self::LESS_OR_EQUAL_THAN, self::LIKE], 'array' => [self::IN, self::NOT_IN], 'boolean' => [self::EQUALS, self::NOT_EQUALS], 'NULL' => [self::NULL, self::NOT_NULL]];
}
