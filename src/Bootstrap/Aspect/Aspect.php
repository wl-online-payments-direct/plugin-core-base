<?php

namespace WOP\OnlinePayments\Core\Bootstrap\Aspect;

/**
 * Interface Aspect
 *
 * @package OnlinePayments\Core\Bootstrap\Aspect
 */
interface Aspect
{
    public function applyOn($callee, array $params = []);
}
