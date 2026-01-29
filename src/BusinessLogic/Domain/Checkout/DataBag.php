<?php

namespace WOP\OnlinePayments\Core\BusinessLogic\Domain\Checkout;

/**
 * Class DataBag
 *
 * @package OnlinePayments\Core\BusinessLogic\Domain\Checkout
 */
class DataBag
{
    /**
     * @var array<string, mixed>
     */
    private array $data;
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }
    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
