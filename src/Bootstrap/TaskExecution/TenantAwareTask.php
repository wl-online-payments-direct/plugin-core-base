<?php

namespace WOP\OnlinePayments\Core\Bootstrap\TaskExecution;

use WOP\OnlinePayments\Core\BusinessLogic\Domain\Multistore\TenantContext;
use WOP\OnlinePayments\Core\Infrastructure\TaskExecution\Task;
/**
 * Class TenantAwareTask
 *
 * @package OnlinePayments\Core\Bootstrap\TaskExecution
 */
abstract class TenantAwareTask extends Task
{
    protected string $tenantId;
    public function __construct(string $tenantId)
    {
        $this->tenantId = $tenantId;
    }
    /**
     * Wraps doExecute() in tenant context. Subclasses implement doExecute().
     */
    final public function execute(): void
    {
        TenantContext::doWithTenant($this->tenantId, function () {
            $this->doExecute();
        });
    }
    abstract protected function doExecute(): void;
}
