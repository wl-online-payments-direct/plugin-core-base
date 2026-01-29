<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Serializer\Interfaces;

/**
 * Interface Serializable
 *
 * @package OnlinePayments\Core\Infrastructure\Serializer\Interfaces
 */
interface Serializable
{
    /**
     * Transforms array into an serializable object,
     *
     * @param array $array Data that is used to instantiate serializable object.
     *
     * @return Serializable
     *      Instance of serialized object.
     */
    public static function fromArray(array $array): Serializable;
    /**
     * Transforms serializable object into an array.
     *
     * @return array Array representation of a serializable object.
     */
    public function toArray(): array;
}
