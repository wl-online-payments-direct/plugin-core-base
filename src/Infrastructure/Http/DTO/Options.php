<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Http\DTO;

use WOP\OnlinePayments\Core\Infrastructure\Data\DataTransferObject;
/**
 * Class Options.
 *
 * @package OnlinePayments\Core\Infrastructure\Http\DTO
 */
class Options extends DataTransferObject
{
    /**
     * Name of the option.
     *
     * @var string
     */
    private string $name;
    /**
     * Value of the option.
     *
     * @var string
     */
    private string $value;
    /**
     * Options constructor.
     *
     * @param string $name Name of the option.
     * @param string $value Value of the option.
     */
    public function __construct(string $name, string $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
    /**
     * Gets name of the option.
     *
     * @return string Name of the option.
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * Gets value of the option.
     *
     * @return string Value of the option.
     */
    public function getValue(): string
    {
        return $this->value;
    }
    /**
     * Transforms DTO to an array representation.
     *
     * @return array DTO in array format.
     */
    public function toArray(): array
    {
        return ['name' => $this->getName(), 'value' => $this->getValue()];
    }
    /**
     * Transforms raw array data to Options.
     *
     * @param array $data Raw array data.
     *
     * @return Options Transformed object.
     */
    public static function fromArray(array $data): DataTransferObject
    {
        return new static($data['name'], $data['value']);
    }
}
