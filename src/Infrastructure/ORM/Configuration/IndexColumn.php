<?php

namespace WOP\OnlinePayments\Core\Infrastructure\ORM\Configuration;

use InvalidArgumentException;
/**
 * Class Index.
 *
 * @package OnlinePayments\Core\Infrastructure\ORM\Configuration
 */
class IndexColumn
{
    /**
     * Boolean index type.
     */
    const BOOLEAN = 'boolean';
    /**
     * DateTime index type.
     */
    const DATETIME = 'dateTime';
    /**
     * Double number index type.
     */
    const DOUBLE = 'double';
    /**
     * Integer number index type.
     */
    const INTEGER = 'integer';
    /**
     * String index type.
     */
    const STRING = 'string';
    /**
     * Index type.
     *
     * @var string
     */
    private string $type;
    /**
     * Property name (column name).
     *
     * @var string
     */
    private string $property;
    /**
     * Index constructor.
     *
     * @param string $type Type of index. User this class constants for types.
     * @param string $property Column name.
     */
    public function __construct(string $type, string $property)
    {
        if (!in_array($type, [self::BOOLEAN, self::DATETIME, self::DOUBLE, self::INTEGER, self::STRING], \true)) {
            throw new InvalidArgumentException("Invalid index type given: {$type}.");
        }
        $this->type = $type;
        $this->property = $property;
    }
    /**
     * Returns property name.
     *
     * @return string Property name.
     */
    public function getProperty(): string
    {
        return $this->property;
    }
    /**
     * Returns index field type.
     *
     * @return string Field type.
     */
    public function getType(): string
    {
        return $this->type;
    }
}
