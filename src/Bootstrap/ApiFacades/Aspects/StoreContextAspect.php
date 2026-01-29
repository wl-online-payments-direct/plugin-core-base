<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Aspects;

use Exception;
use WOP\OnlinePayments\Core\Bootstrap\Aspect\Aspect;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\StoreContext;
/**
 * Class StoreContextAspect.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\Aspects
 */
class StoreContextAspect implements Aspect
{
    /**
     * @var string
     */
    private string $storeId;
    /**
     * @param string $storeId
     */
    public function __construct(string $storeId)
    {
        $this->storeId = $storeId;
    }
    /**
     * @throws Exception
     */
    public function applyOn($callee, array $params = [])
    {
        return StoreContext::doWithStore($this->storeId, $callee, $params);
    }
}
