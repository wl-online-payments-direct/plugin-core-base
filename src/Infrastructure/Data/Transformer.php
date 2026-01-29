<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Data;

/**
 * Class Transformer.
 *
 * @package OnlinePayments\Core\Infrastructure\Data
 */
class Transformer
{
    /**
     * Transforms data transfer object to different format.
     *
     * @param DataTransferObject $transformable Object to be transformed.
     *
     * @return array Transformed result.
     *
     */
    public static function transform(DataTransferObject $transformable): array
    {
        return $transformable->toArray();
    }
    /**
     * Transforms a batch of transformable object.
     *
     * @param DataTransferObject[] $batch Batch of transformable objects.
     *
     * @return array Batch of transformed objects.
     */
    public static function batchTransform(array $batch): array
    {
        $result = [];
        foreach ($batch as $index => $transformable) {
            $result[$index] = static::transform($transformable);
        }
        return $result;
    }
    /**
     * Trims empty arrays or null values.
     *
     * @param array $data
     *
     * @return void
     */
    protected static function trim(array &$data): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                static::trim($data[$key]);
            }
            if ($value === null || is_array($value) && empty($value)) {
                unset($data[$key]);
            }
        }
    }
}
