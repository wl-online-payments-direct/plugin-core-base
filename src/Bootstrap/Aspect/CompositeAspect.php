<?php

namespace WOP\OnlinePayments\Core\Bootstrap\Aspect;

/**
 * Class CompositeAspect
 *
 * @package OnlinePayments\Core\Bootstrap\Aspect
 */
class CompositeAspect implements Aspect
{
    /**
     * @var Aspect
     */
    private Aspect $aspect;
    /**
     * @var Aspect|null
     */
    private ?Aspect $next = null;
    public function __construct(Aspect $aspect)
    {
        $this->aspect = $aspect;
    }
    public function append(Aspect $aspect): void
    {
        $this->next = new self($aspect);
    }
    /**
     * @throws \Exception
     */
    public function applyOn($callee, array $params = [])
    {
        $callback = $callee;
        if ($this->next) {
            $callback = $this->getNextCallee($callee, $params);
        }
        return $this->aspect->applyOn($callback, $params);
    }
    private function getNextCallee($callee, array $params = []): \Closure
    {
        return function () use ($callee, $params) {
            return $this->next->applyOn($callee, $params);
        };
    }
}
