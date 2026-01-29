<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Serializer;

use WOP\OnlinePayments\Core\Infrastructure\ServiceRegister;
/**
 * Class Serializer
 *
 * @package OnlinePayments\Core\Infrastructure\Serializer
 */
abstract class Serializer
{
    /**
     * string CLASS_NAME Class name identifier.
     */
    const CLASS_NAME = __CLASS__;
    /**
     * Serializes data.
     *
     * @param mixed $data Data to be serialized.
     *
     * @return mixed String representation of the serialized data.
     */
    public static function serialize($data): string
    {
        /** @var Serializer $instace */
        $instance = ServiceRegister::getService(self::CLASS_NAME);
        return $instance->doSerialize($data);
    }
    /**
     * Unserializes data.
     *
     * @param string $serialized Serialized data.
     *
     * @return mixed Unserialized data.
     */
    public static function unserialize(string $serialized)
    {
        /** @var Serializer $instace */
        $instance = ServiceRegister::getService(self::CLASS_NAME);
        return $instance->doUnserialize($serialized);
    }
    /**
     * Serializes data.
     *
     * @param mixed $data Data to be serialized.
     *
     * @return string String representation of the serialized data.
     */
    abstract protected function doSerialize($data): string;
    /**
     * Unserializes data.
     *
     * @param string $serialized Serialized data.
     *
     * @return mixed Unserialized data.
     */
    abstract protected function doUnserialize(string $serialized);
}
