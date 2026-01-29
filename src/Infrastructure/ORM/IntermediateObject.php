<?php

namespace WOP\OnlinePayments\Core\Infrastructure\ORM;

/**
 * Class IntermediateObject.
 *
 * @package OnlinePayments\Core\Infrastructure\ORM
 */
class IntermediateObject
{
    /**
     * @var ?string
     */
    private ?string $index1 = null;
    /**
     * @var ?string
     */
    private ?string $index2 = null;
    /**
     * @var ?string
     */
    private ?string $index3 = null;
    /**
     * @var ?string
     */
    private ?string $index4 = null;
    /**
     * @var ?string
     */
    private ?string $index5 = null;
    /**
     * @var ?string
     */
    private ?string $index6 = null;
    /**
     * @var ?string
     */
    private ?string $data = null;
    /**
     * @var array
     */
    private array $otherIndexes = [];
    /**
     * @return string
     */
    public function getIndex1(): string
    {
        return $this->index1;
    }
    /**
     * @param string|null $index1
     */
    public function setIndex1(?string $index1): void
    {
        $this->index1 = $index1;
    }
    /**
     * @return string
     */
    public function getIndex2(): string
    {
        return $this->index2;
    }
    /**
     * @param string|null $index2
     */
    public function setIndex2(?string $index2): void
    {
        $this->index2 = $index2;
    }
    /**
     * @return string
     */
    public function getIndex3(): string
    {
        return $this->index3;
    }
    /**
     * @param string|null $index3
     */
    public function setIndex3(?string $index3): void
    {
        $this->index3 = $index3;
    }
    /**
     * @return string
     */
    public function getIndex4(): string
    {
        return $this->index4;
    }
    /**
     * @param string|null $index4
     */
    public function setIndex4(?string $index4): void
    {
        $this->index4 = $index4;
    }
    /**
     * @return string
     */
    public function getIndex5(): string
    {
        return $this->index5;
    }
    /**
     * @param string|null $index5
     */
    public function setIndex5(?string $index5): void
    {
        $this->index5 = $index5;
    }
    /**
     * @return string
     */
    public function getIndex6(): string
    {
        return $this->index6;
    }
    /**
     * @param string|null $index6
     */
    public function setIndex6(?string $index6): void
    {
        $this->index6 = $index6;
    }
    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }
    /**
     * @param string $data
     */
    public function setData(string $data)
    {
        $this->data = $data;
    }
    /**
     * Sets index value
     *
     * @param int $index
     * @param string|null $value
     *
     * @return void
     */
    public function setIndexValue(int $index, ?string $value): void
    {
        if ($index < 1) {
            return;
        }
        if ($index <= 6) {
            $methodName = 'setIndex' . $index;
            $this->{$methodName}($value);
        } else {
            $this->otherIndexes['index_' . $index] = $value;
        }
    }
    /**
     * Returns index value
     *
     * @param int $index
     *
     * @return string|null
     */
    public function getIndexValue(int $index): ?string
    {
        $value = null;
        if ($index < 1) {
            return null;
        }
        if ($index <= 6) {
            $methodName = 'getIndex' . $index;
            $value = $this->{$methodName}();
        } elseif (array_key_exists('index_' . $index, $this->otherIndexes)) {
            $value = $this->otherIndexes['index_' . $index];
        }
        return $value;
    }
}
