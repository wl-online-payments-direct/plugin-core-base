<?php

namespace WOP\OnlinePayments\Core\Infrastructure\Logger;

use WOP\OnlinePayments\Core\Infrastructure\Data\DataTransferObject;
/**
 * Class LogContextData.
 *
 * @package OnlinePayments\Core\Infrastructure\Logger
 */
class LogContextData extends DataTransferObject
{
    /**
     * Name of data.
     *
     * @var string
     */
    private string $name;
    /**
     * Value of data.
     *
     * @var mixed
     */
    private $value;
    /**
     * LogContextData constructor.
     *
     * @param string $name Name of data.
     * @param mixed $value Value of data.
     */
    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
    /**
     * Gets name of data.
     *
     * @return string Name of data.
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * Gets value of data.
     *
     * @return mixed Value of data.
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @inheritDoc
     */
    public static function fromArray(array $data): DataTransferObject
    {
        return new self($data['name'], $data['value']);
    }
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return ['name' => $this->getName(), 'value' => $this->getValue()];
    }
}
