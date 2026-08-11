<?php

namespace WOP\OnlinePayments\Core\Bootstrap\ApiFacades\Aspects;

use WOP\OnlinePayments\Core\Bootstrap\Aspect\Aspect;
use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\TenantContext;
/**
 * Class TenantContextAspect.
 *
 * @package OnlinePayments\Core\Bootstrap\ApiFacades\Aspects
 */
class TenantContextAspect implements Aspect
{
    /**
     * @var string
     */
    private string $tenantId;
    /**
     * @param string $tenantId
     */
    public function __construct(string $tenantId)
    {
        $this->tenantId = $tenantId;
    }
    public function applyOn($callee, array $params = [])
    {
        return TenantContext::doWithTenant($this->tenantId, $callee, $params);
    }
}
