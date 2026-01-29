<?php

namespace WOP\OnlinePayments\Core\Bootstrap;

/**
 * Class SingleInstance
 *
 * @package OnlinePayments\Core\Bootstrap
 */
class SingleInstance
{
    /**
     * @var mixed
     */
    private $instance;
    /**
     * @var callable
     */
    private $delegate;
    /**
     * @param callable $delegate
     */
    public function __construct(callable $delegate)
    {
        $this->delegate = $delegate;
    }
    /**
     * @return mixed
     */
    public function __invoke()
    {
        if (!$this->instance) {
            $this->instance = call_user_func($this->delegate);
        }
        return $this->instance;
    }
}
