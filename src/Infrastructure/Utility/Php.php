<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Utility;

/**
 * Class Php.
 *
 * @package OnlinePayments\Core\Infrastructure\Utility
 */
class Php
{
    /**
     * @param $class
     *
     * @return array
     */
    public static function classUsesRecursive($class): array
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $results = [];
        foreach (array_reverse(class_parents($class)) + [$class => $class] as $c) {
            /** @noinspection SlowArrayOperationsInLoopInspection */
            $results = array_merge($results, static::traitUsesRecursive($c));
        }
        return array_unique($results);
    }
    /**
     * @param $trait
     *
     * @return array|false|string[]
     */
    public static function traitUsesRecursive($trait)
    {
        $traits = class_uses($trait) ?: [];
        foreach ($traits as $t) {
            /** @noinspection SlowArrayOperationsInLoopInspection */
            $traits = array_merge($traits, static::traitUsesRecursive($t));
        }
        return $traits;
    }
}
